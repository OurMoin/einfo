<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;

class PostController extends Controller
{
    /**
     * Display a listing of the resource with pagination.
     */
   public function index(Request $request)
{
    $posts = Post::with(['user', 'category', 'comments.user']) // comments সহ লোড হবে
        ->latest()
        ->paginate(3);
    if ($request->ajax()) {
        return response()->json([
            'posts' => view('frontend.posts-partial', compact('posts'))->render(),
            'hasMore' => $posts->hasMorePages()
        ]);
    }
    return view("frontend.index", compact('posts'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_id = Auth::id(); // লগইন করা ইউজারের ID
        
        // Validation
        $request->validate([
            'category_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048|required_without:description',
            'description' => 'nullable|string|max:1000|required_without:image',
        ]);

        $categoryId = null;
        $newCategory = null;

        // Check if category_id is provided (existing category selected)
        if ($request->filled('category_id') && $request->category_id != '') {
            // Validate that the category exists
            $categoryExists = Category::where('id', $request->category_id)->exists();
            if ($categoryExists) {
                $categoryId = $request->category_id;
            } else {
                // If category_id doesn't exist, treat as new category
                $newCategory = $request->category_name;
            }
        } else {
            // User typed a new category name
            $newCategory = $request->category_name;
        }

        // Handle image upload
        $imageName = null;
        if($request->hasFile('image')){
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads'), $imageName);
        }
       
        // DB-এ সেভ করা
        Post::create([
            'title' => $request->title,
            'price' => $request->price,
            'highest_price' => $request->discount ?? null,
            'image' => $imageName,
            'description' => $request->description,
            'user_id' => $user_id,
            'category_id' => $categoryId, // Will be null if new category
            'new_category' => $newCategory, // Will be null if existing category
        ]);
       
        return back()->with('success', 'Post created successfully!');
    }

    // Updated method for category-based posts with subcategory support
    public function showByCategory(Request $request, $slug, $subcategorySlug = null)
    {
        // Main category খুঁজুন
        $category = Category::where('slug', $slug)->first();
        
        if (!$category) {
            abort(404, 'Category not found');
        }
        
        $currentSubcategory = null;
        
        if ($subcategorySlug) {
            // Subcategory আছে কিনা check করুন
            $currentSubcategory = Category::where('slug', $subcategorySlug)
                                        ->where('parent_cat_id', $category->id)
                                        ->first();
            
            if (!$currentSubcategory) {
                abort(404, 'Subcategory not found');
            }
            
            // Subcategory এর posts/users নিন
            $targetCategoryId = $currentSubcategory->id;
        } else {
            // Main category এর সব posts/users নিন (including subcategories)
            $categoryIds = collect([$category->id]);
            
            // সব child categories add করুন
            $childCategories = Category::where('parent_cat_id', $category->id)->pluck('id');
            $categoryIds = $categoryIds->merge($childCategories);
            
            $targetCategoryId = $categoryIds->toArray();
        }
        
        // Check if there are users with this category_id (profile data)
        if ($subcategorySlug) {
            $hasUsers = User::where('category_id', $targetCategoryId)->exists();
        } else {
            $hasUsers = User::whereIn('category_id', $targetCategoryId)->exists();
        }
        
        if ($hasUsers) {
            // Load users if they exist for this category
            if ($subcategorySlug) {
                $posts = User::where('category_id', $targetCategoryId)
                            ->with('category');
            } else {
                $posts = User::whereIn('category_id', $targetCategoryId)
                            ->with('category');
            }
        } else {
            // Load regular posts for product/service categories  
            if ($subcategorySlug) {
                $posts = Post::with(['user', 'category'])
                             ->where('category_id', $targetCategoryId)
                             ->latest();
            } else {
                $posts = Post::with(['user', 'category'])
                             ->whereIn('category_id', $targetCategoryId)
                             ->latest();
            }
        }
        
        // Sorting handle করুন
        if ($request->get('sort')) {
            switch ($request->get('sort')) {
                case 'price-low':
                    $posts = $posts->orderBy('price', 'asc');
                    break;
                case 'price-high':
                    $posts = $posts->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $posts = $posts->orderBy('created_at', 'desc');
                    break;
                default:
                    $posts = $posts->latest();
            }
        }
        
        $posts = $posts->paginate(12);
        
        // Ensure variables are always set
        $currentSubcategory = $currentSubcategory ?? null;
        $currentSubsubcategory = $currentSubsubcategory ?? null;
        
        // AJAX request এর জন্য
        if ($request->ajax()) {
            return response()->json([
                'posts' => view('frontend.products-partial', compact('posts'))->render(),
                'hasMore' => $posts->hasMorePages()
            ]);
        }
        
        return view('frontend.products', [
            'posts' => $posts,
            'category' => $category,
            'currentSubcategory' => $currentSubcategory,
            'currentSubsubcategory' => $currentSubsubcategory
        ]);
    }
   
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
   
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
   
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
   
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
       
        // Check if the authenticated user owns this post
        if ($post->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this post.'
            ], 403);
        }
       
        // Delete the image file if it exists
        if ($post->image) {
            $imagePath = public_path('uploads/' . $post->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
       
        // Delete the post from database
        $post->delete();
       
        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully!'
        ]);
    }
}