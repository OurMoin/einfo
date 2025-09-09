{{-- resources/views/frontend/products-partial.blade.php --}}
@forelse($posts as $post)
<div class="col-4">
   <div class="card shadow-sm border-0">
      @if($post->image)
         <img src="{{ asset('uploads/'.$post->image) }}" class="card-img-top" alt="{{ $post->title }}">
      @else
         <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top" alt="No Image">
      @endif
      
      <div class="card-body p-2">
         <h5 class="card-title mb-0">{{ $post->title }}</h5>
         @if($post->discount)
            <small class="price-tag text-danger"><s>{{ $post->price }}</s></small>
            <small class="price-tag text-success">{{ $post->discount }}</small>
         @else
            <small class="price-tag text-success">{{ $post->price }}</small>
         @endif
         <span class="badge bg-primary cart-badge">
            <i class="bi bi-cart-plus"></i>
         </span>                            
      </div>
   </div>
</div>
@empty
<div class="col-12">
   <div class="text-center py-5">
      <p class="text-muted">No products found in this category.</p>
   </div>
</div>
@endforelse