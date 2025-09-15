<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SmsController;

Route::get('/send', [LocationController::class, 'sendOtp']);
Route::post('/verify-otp', [LocationController::class, 'verifyOtp'])->name('verify.otp');
Route::get('/resend-otp', [LocationController::class, 'reSendOtp']);
Route::get('/get-cities/{country_id}', [LocationController::class, 'getCities']);

// Home route with pagination
Route::get('/', [PostController::class, 'index']);

// AJAX routes
Route::get('/posts/load-more', [PostController::class, 'index'])->name('posts.loadmore');
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
    
    // Buy and Sell routes
    Route::get('/buy', [OrderController::class, 'buyPage'])->name('buy');
    Route::get('/sell', [OrderController::class, 'sellPage'])->name('sell');
});

Route::post('/comment/store', [CommentController::class, 'commentStore'])->name('comment.store');

// Products routes - এই order টা important
Route::get('/products', function () {
    return view('frontend.products');
});

// Level 3: Sub-subcategory (সবার আগে থাকতে হবে)
Route::get('/products/{category}/{subcategory}/{subsubcategory}', [PostController::class, 'showByCategory'])->name('products.category.subsub');

// Level 2: Subcategory  
Route::get('/products/{category}/{subcategory}', [PostController::class, 'showByCategory'])->name('products.category.sub');

// Level 1: Main category (সবার শেষে থাকতে হবে)
Route::get('/products/{category}', [PostController::class, 'showByCategory'])->name('products.category');

// Add this route for post deletion
Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy')->middleware('auth');

// Order routes
Route::middleware('auth')->group(function () {
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
});

Route::middleware('auth')->group(function () {
    // Follow a user
    Route::post('/follow/{user}', [LocationController::class, 'follow'])->name('user.follow');

    // Unfollow a user
    Route::post('/unfollow/{user}', [LocationController::class, 'unfollow'])->name('user.unfollow');
});





Route::get('/sms', [SmsController::class, 'index'])->name('sms.form');
Route::post('/sms', [SmsController::class, 'send'])->name('sms.send');


Route::get('/login-success/{identifier}', function ($identifier) {
    return redirect('/'.$identifier);
})->name('login.success');

// Logout success - 300ms পর home এ redirect
Route::get('/logout-success/{identifier}', function ($identifier) {
     return redirect('/');
})->name('logout.success');


require __DIR__.'/auth.php';
Route::get('/{username}', [LocationController::class, 'show'])->name('profile.show');