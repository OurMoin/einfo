@php
    $visibleItemsCount = 0;
@endphp

<div class="row g-3 g-md-4 mb-4" id="posts-container">
    @forelse($posts as $item)
        @php
            // Check if this is a User object (profile) or Post object
            $isUserProfile = !isset($item->title);
            
            if ($isUserProfile) {
                // This is a User object
                $isOwnProfile = auth()->check() && auth()->id() == $item->id;
                $categoryType = 'profile'; // Manually set as profile for users
                
                // Profile card শুধুমাত্র তখনই show করবে যখন email_verified = 0 হবে
                $shouldShowCard = ($item->email_verified === 0);
            } else {
                // This is a Post object
                $isOwnPost = auth()->check() && auth()->id() == $item->user_id;
                $categoryType = $item->category->cat_type ?? 'product';
                
                // Post card সব সময় show করবে (email verification check নাই)
                $shouldShowCard = true;
            }
            
            // If card should be shown, increment counter
            if ($shouldShowCard) {
                $visibleItemsCount++;
            }
        @endphp
        
        @if($shouldShowCard)
        <div class="col-4">
           <div class="card shadow-sm border-0">
              @if(isset($item->title))
                 {{-- This is a Post --}}
                 @if($item->image)
                    <img src="{{ asset('uploads/'.$item->image) }}" class="card-img-top" alt="Post Image">
                 @else
                    <img src="{{ asset('profile-image/no-image.jpeg') }}" class="card-img-top" alt="No Image">
                 @endif
              @else
                 {{-- This is a User (Profile) --}}
                 @if($item->image)
                    <img src="{{ asset('profile-image/'.$item->image) }}" class="card-img-top" alt="Profile Image">
                 @else
                    <img src="{{ asset('profile-image/no-image.jpeg') }}" class="card-img-top" alt="No Image">
                 @endif
              @endif
             
              <div class="card-body p-2">
                 @if($isUserProfile)
                    {{-- Profile card layout --}}
                    <a href="{{ route('profile.show', $item->username) }}">
           <h5 class="card-title mb-0">
              {{ $item->name ? Str::limit($item->name, 20) : 'No Name' }}
           </h5>
        </a>
                    <small class="price-tag text-success">{{ $item->area ? Str::limit($item->area, 20) : 'No area' }}</small>
                    
                    <span class="badge {{ $isOwnProfile ? 'bg-secondary' : 'bg-primary' }} cart-badge {{ $isOwnProfile ? 'disabled' : '' }}">
                       <i class="bi bi-telephone"></i>
                    </span>
                 <!-- Fixed Product Card Section -->
@else
    {{-- Regular product/service card layout --}}
    <h5 class="card-title mb-0">{{ $item->title ? Str::limit($item->title, 20) : 'No Title' }}</h5>
    <small class="price-tag text-success">{{ $item->price ? '৳' . $item->price : 'No price' }}</small>
    
    @if(!$isOwnPost)
        {{-- Only show add to cart for others' posts --}}
        <button class="btn btn-primary btn-sm mt-2 add-to-cart-btn" 
                data-product-id="{{ $item->id }}" 
                data-product-name="{{ $item->title }}" 
                data-product-price="{{ $item->price ?? 0 }}" 
                data-product-image="{{ $item->image ? asset('uploads/'.$item->image) : asset('profile-image/no-image.jpeg') }}">
            @if($categoryType == 'service')
                <i class="bi bi-calendar-check"></i> Book Now
            @else
                <i class="bi bi-cart-plus"></i> Add to Cart
            @endif
        </button>
    @else
        {{-- Show disabled badge for own posts --}}
        <span class="badge bg-secondary cart-badge disabled mt-2">
            @if($categoryType == 'service')
                <i class="bi bi-calendar-check"></i> Your Service
            @else
                <i class="bi bi-cart-plus"></i> Your Product
            @endif
        </span>
    @endif
@endif

<!-- Event Delegation JavaScript (add this once in master.blade.php) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event delegation for add to cart buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            const button = e.target.closest('.add-to-cart-btn');
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const productPrice = button.getAttribute('data-product-price');
            const productImage = button.getAttribute('data-product-image');
            
            console.log('Add to cart clicked:', {productId, productName, productPrice, productImage});
            
            // Check if cart system is loaded
            if (typeof cart === 'undefined') {
                console.error('Cart system not loaded!');
                alert('Cart system not loaded. Please refresh the page.');
                return;
            }
            
            // Validate data
            if (!productId || !productName) {
                console.error('Missing product data');
                return;
            }
            
            // Add to cart
            cart.addToCart(productId, productName, productPrice || 0, productImage);
            
            // Optional: Add button animation
            button.classList.add('btn-success');
            button.innerHTML = '<i class="bi bi-check2"></i> Added!';
            setTimeout(() => {
                button.classList.remove('btn-success');
                button.innerHTML = '<i class="bi bi-cart-plus"></i> Add to Cart';
            }, 2000);
        }
    });
    
    // Debug: Check if cart system is loaded
    setTimeout(() => {
        if (typeof cart === 'undefined') {
            console.error('❌ Cart system not loaded properly');
        } else {
            console.log('✅ Cart system loaded successfully');
        }
    }, 1000);
});
</script>                           
              </div>
           </div>
        </div>
        @endif
    @empty
    <div class="col-12">
       <div class="text-center py-5">
          <p class="text-muted">Nothing is found!</p>
       </div>
    </div>
    @endforelse
    
    {{-- যদি posts আছে কিন্তু কোনটাই দেখানো হচ্ছে না (সব hide হয়ে গেছে) --}}
    @if($posts->count() > 0 && $visibleItemsCount == 0)
    <div class="col-12">
       <div class="text-center py-5">
          <p class="text-muted">Nothing is found!</p>
       </div>
    </div>
    @endif
</div>