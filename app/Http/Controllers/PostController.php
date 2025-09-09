<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Post;
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
        // Validation: image বা description যেকোনো একটাই required
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048|required_without:description',
            'description' => 'nullable|string|max:1000|required_without:image',
            'category_id' => 'required|exists:categories,id',
        ]);
       
        $imageName = null;
        if($request->hasFile('image')){
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads'), $imageName);
        }
       
        // DB-এ সেভ করা
        Post::create([
    'title' => $request->title,
    'price' => $request->price,
    'highest_price' => $request->discount,
    'image' => $imageName,
    'description' => $request->description,
    'user_id' => $user_id,
    'category_id' => $request->category_id
]);
       
        return back()->with('success', 'Post created successfully!');
    }


    // PostController এ এই method add করুন
public function showByCategory($slug, Request $request)
{
    // Slug দিয়ে category খুঁজুন
    $category = \App\Models\Category::where('slug', $slug)->first();
    
    if (!$category) {
        abort(404, 'Category not found');
    }
    
    // Category অনুযায়ী posts নিন
    $posts = Post::with(['user', 'category'])
                 ->where('category_id', $category->id)
                 ->latest()
                 ->paginate(12);
    
    // AJAX request এর জন্য
    if ($request->ajax()) {
        return response()->json([
            'posts' => view('frontend.products-partial', compact('posts'))->render(),
            'hasMore' => $posts->hasMorePages()
        ]);
    }
    
    return view('frontend.products', compact('posts', 'category'));
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