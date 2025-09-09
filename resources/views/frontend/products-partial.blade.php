<div class="row g-3 g-md-4 mb-4" id="posts-container">
 
    @forelse($posts as $item)
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
         @php
            // Check if this is a User object (profile) or Post object
            $isUserProfile = !isset($item->title);
            
            if ($isUserProfile) {
                // This is a User object
                $isOwnProfile = auth()->check() && auth()->id() == $item->id;
                $categoryType = 'profile'; // Manually set as profile for users
            } else {
                // This is a Post object
                $isOwnPost = auth()->check() && auth()->id() == $item->user_id;
                $categoryType = $item->category->cat_type ?? 'product';
            }
         @endphp
         
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
         @else
            {{-- Regular product/service card layout --}}
            <h5 class="card-title mb-0">{{ $item->title ? Str::limit($item->title, 20) : 'No Title' }}</h5>
            <small class="price-tag text-success">{{ $item->price ? Str::limit($item->price, 20) : 'No price' }}</small>
            
            <span class="badge {{ $isOwnPost ? 'bg-secondary' : 'bg-primary' }} cart-badge {{ $isOwnPost ? 'disabled' : '' }}">
               @if($categoryType == 'service')
                  <i class="bi bi-calendar-check"></i>
               @else
                  <i class="bi bi-cart-plus"></i>
               @endif
            </span>
         @endif                            
      </div>
   </div>
</div>
@empty
<div class="col-12">
   <div class="text-center py-5">
      <p class="text-muted">No posts found.</p>
   </div>
</div>
@endforelse
</div>