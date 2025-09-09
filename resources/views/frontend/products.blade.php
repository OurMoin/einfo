@extends("frontend.master")
@section('main-content')

<style>
   .card-img-top {
   height: 100px;
   object-fit: cover;
   width: 100%;
   }
   .card-title {
   display: inline-block;
   width: 100%;
   white-space: nowrap;
   overflow: hidden;
   text-overflow: ellipsis;
   }
   .price-tag {
   display: inline-block;
   max-width: 100px;
   white-space: nowrap;
   overflow: hidden;
   text-overflow: ellipsis;
   }
   a {
   text-decoration: none;
   }
   @media (max-width: 767.98px) {
   .card-title {
   font-size: 0.9rem;
   }
   .price-tag {
   font-size: 0.7rem;
   }
   }
   @media (max-width: 575.98px) {
   .card-title {
   font-size: 0.8rem;
   }
   .price-tag {
   font-size: 0.7rem;
   max-width: 70px;
   }
   }
   .cart-badge {
   float: right;
   }
   @media (max-width: 400px) {
   .cart-badge {
   float: none;
   display: block;
   width: 100%;
   text-align: center;
   margin-top: 8px;
   }
   }
   .top-badge {
   position: absolute;
   top: 10px;
   left: 10px;
   padding: 4px;
   font-size: 0.6rem;
   z-index: 10;
   text-transform: uppercase;
   }
</style>

<div class="mt-4">
   <style>
      .scroll-container {
      overflow-x: auto;
      overflow-y: hidden;
      white-space: nowrap;
      }
      @media (max-width: 768px) {
      .scroll-container {
      scrollbar-width: none;
      -ms-overflow-style: none;
      }
      .scroll-container::-webkit-scrollbar {
      display: none;
      }
      }
      @media (min-width: 769px) {
      .scroll-container::-webkit-scrollbar {
      height: 8px;
      }
      .scroll-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
      }
      .scroll-container::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 4px;
      }
      .scroll-container::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
      }
      }
      .scroll-content {
      display: inline-flex;
      gap: 15px;
      min-width: 100%;
      }
      .nav-item-custom {
      flex-shrink: 0;
      background: white;
      border-radius: 8px;
      padding: 3px 10px 4px 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 1px solid #c8c8c8;
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 500;
      color: #6c757d;
      text-decoration: none;
      white-space: nowrap;
      }
      .nav-item-custom.active {
      background: #e3f0ffff;
      color: #087fffff;
      border-color: #087fffff;
      }
      .nav-icon {
      font-size: 1.1em;
      }
      .nav-item-custom:not(.active):hover {
      background: #f8f9fa;
      }
   </style>
   
   <div class="container-fluid container">
      
      
      <!-- Horizontal Scrollable Navigation -->
    <!-- Horizontal Scrollable Navigation -->
<div class="scroll-container">
    <div class="scroll-content">
        <a href="/" class="nav-item-custom">
            <span><i class="bi bi-house"></i></span>
        </a>
        
        <a href="#" class="nav-item-custom dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <span><i class="bi bi-funnel"></i> Filter</span>
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="sortBy('price-low')">Price: Low to High</a></li>
            <li><a class="dropdown-item" href="#" onclick="sortBy('price-high')">Price: High to Low</a></li>
            <li><a class="dropdown-item" href="#" onclick="sortBy('best-selling')">Best Selling</a></li>
            <li><a class="dropdown-item" href="#" onclick="sortBy('newest')">Newest First</a></li>
            <li><a class="dropdown-item" href="#" onclick="sortBy('rating')">Highest Rated</a></li>
        </ul>
        
        @if(isset($category) && $category->parent_cat_id)
            {{-- যদি কোন specific category select করা থাকে তাহলে same parent এর অধীনের সব categories --}}
            @php
                $siblingCategories = \App\Models\Category::where('parent_cat_id', $category->parent_cat_id)
                                                       ->whereIn('cat_type', ['product', 'service'])
                                                       ->get();
            @endphp
            
            @foreach($siblingCategories as $siblingCat)
                <a href="{{ route('products.category', $siblingCat->slug) }}" 
                   class="nav-item-custom {{ $category->id == $siblingCat->id ? 'active' : '' }}">
                    <span>{{ $siblingCat->category_name }}</span>
                </a>
            @endforeach
            
        @else
            {{-- যদি কোন specific category select না করা থাকে তাহলে সব parent categories --}}
            @php
                $parentCategories = \App\Models\Category::where('cat_type', 'universal')
                                                       ->whereNull('parent_cat_id')
                                                       ->get();
            @endphp
            
            @foreach($parentCategories as $parentCat)
                @php
                    $subCategories = \App\Models\Category::where('parent_cat_id', $parentCat->id)
                                                       ->whereIn('cat_type', ['product', 'service'])
                                                       ->get();
                @endphp
                
                @if($subCategories->count() > 0)
                    <div class="dropdown nav-item-custom">
                        <a href="#" class="nav-item-custom dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>{{ $parentCat->category_name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach($subCategories as $subCat)
                                <li>
                                    <a class="dropdown-item" href="{{ route('products.category', $subCat->slug) }}">
                                        {{ $subCat->category_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</div>
   </div>
</div>

<div class="mt-4">
   <div class="content-area container">
      <div class="content-section">
         <div class="row g-3 g-md-4" id="posts-container">
            @include('frontend.products-partial', ['posts' => $posts])
         </div>
         
         <!-- Loading Spinner -->
         <div id="loading" style="display: none; text-align: center; margin: 20px 0;">
            <div class="spinner-border text-primary" role="status">
               <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading more products...</p>
         </div>
         
         <!-- Load More Button -->
         @if($posts->hasMorePages())
         <div id="load-more-container" class="text-center mt-3">
            <button id="load-more-btn" class="btn btn-primary">Load More Products</button>
         </div>
         @endif
      </div>
   </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function sortBy(type) {
    console.log('Sorting by:', type);
    // Add your sorting logic here
    switch(type) {
        case 'price-low':
            // Sort products by price low to high
            break;
        case 'price-high':
            // Sort products by price high to low
            break;
        case 'best-selling':
            // Sort by best selling items
            break;
        case 'newest':
            // Sort by newest items
            break;
        case 'rating':
            // Sort by highest rated items
            break;
    }
}

// AJAX Load More Functionality
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let isLoading = false;
    
    const postsContainer = document.getElementById('posts-container');
    const loadingSpinner = document.getElementById('loading');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadMoreContainer = document.getElementById('load-more-container');
    
    @if(isset($category))
    const categorySlug = '{{ $category->slug }}';
    @else
    const categorySlug = null;
    @endif

    // Load More Button Click
    if (loadMoreBtn && categorySlug) {
        loadMoreBtn.addEventListener('click', function() {
            loadMorePosts();
        });
    }

    // Auto Load on Scroll (Optional)
    window.addEventListener('scroll', function() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
            if (!isLoading && loadMoreBtn && loadMoreBtn.style.display !== 'none' && categorySlug) {
                loadMorePosts();
            }
        }
    });

    function loadMorePosts() {
        if (isLoading || !categorySlug) return;
        
        isLoading = true;
        currentPage++;
        
        // Show loading spinner
        loadingSpinner.style.display = 'block';
        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
        
        // AJAX request
        const url = `/products/${categorySlug}`;
        
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                page: currentPage
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                // Hide loading spinner
                loadingSpinner.style.display = 'none';
                
                // Append new posts
                postsContainer.insertAdjacentHTML('beforeend', response.posts);
                
                // Show/Hide load more button
                if (response.hasMore) {
                    if (loadMoreBtn) loadMoreBtn.style.display = 'block';
                } else {
                    if (loadMoreContainer) loadMoreContainer.style.display = 'none';
                }
                
                isLoading = false;
            },
            error: function(xhr, status, error) {
                loadingSpinner.style.display = 'none';
                if (loadMoreBtn) loadMoreBtn.style.display = 'block';
                console.error('Error loading posts:', error);
                isLoading = false;
            }
        });
    }
});
</script>


<script>
// Auto scroll active item to center
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.querySelector('.scroll-container');
    const activeItem = document.querySelector('.nav-item-custom.active');
    
    if (scrollContainer && activeItem) {
        scrollToCenter(activeItem, scrollContainer);
    }
    
    // Handle navigation clicks and scroll to center
    document.querySelectorAll('.nav-item-custom').forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            document.querySelectorAll('.nav-item-custom').forEach(nav => {
                nav.classList.remove('active');
            });
            
            // Add active class to clicked item (if not a dropdown)
            if (!this.classList.contains('dropdown-toggle')) {
                this.classList.add('active');
                scrollToCenter(this, scrollContainer);
            }
        });
    });
});

function scrollToCenter(element, container) {
    const elementRect = element.getBoundingClientRect();
    const containerRect = container.getBoundingClientRect();
    
    // Calculate the position to scroll to center the element
    const elementCenter = elementRect.left + elementRect.width / 2;
    const containerCenter = containerRect.left + containerRect.width / 2;
    const scrollOffset = elementCenter - containerCenter;
    
    // Smooth scroll to center
    container.scrollBy({
        left: scrollOffset,
        behavior: 'smooth'
    });
}
</script>

@endsection