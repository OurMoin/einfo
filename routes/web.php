<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

Route::get('/send', [LocationController::class, 'sendOtp']);
Route::post('/verify-otp', [LocationController::class, 'verifyOtp'])->name('verify.otp');
Route::get('/resend-otp', [LocationController::class, 'reSendOtp']);
Route::get('/get-cities/{country_id}', [LocationController::class, 'getCities']);

// Home route with pagination - এখন PostController ব্যবহার করবে
Route::get('/', [PostController::class, 'index']);

// AJAX route for lazy loading - Home page এর জন্য (সব posts)
Route::get('/posts/load-more', [PostController::class, 'index'])->name('posts.loadmore');

// AJAX route for user-specific posts loading - Profile page এর জন্য
Route::get('/posts/load-more/{userId}', [LocationController::class, 'loadMoreUserPosts'])->name('posts.loadmore.user');

Route::post('/store', [PostController::class, 'store'])->name('post.store');

Route::get('/dashboard', function () {
    $user = Auth::user();
    return redirect('/'.$user->username);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/comment/store', [CommentController::class, 'commentStore'])->name('comment.store');



Route::get('/products', function () {
    return view('frontend.products'); // resources/views/frontend/products.blade.php
});

// web.php এ add করুন
Route::get('/products/{slug}', [PostController::class, 'showByCategory'])->name('products.category');


// Add this route for post deletion
Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy')->middleware('auth');

require __DIR__.'/auth.php';

Route::get('/{username}', [LocationController::class, 'show'])->name('profile.show');