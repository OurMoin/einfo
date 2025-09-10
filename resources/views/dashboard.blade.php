@extends("frontend.master")
@section('main-content')
<div class="container mt-4">
<!-- Dashboard Content -->
<div class="row mt-3">
   @auth
   @if(is_null(Auth::user()->email_verified_at))
   {{-- যদি ইমেইল ভেরিফাই না করা থাকে --}}
   <div class="mb-4">
      <div class="card border-warning">
         <div class="card-body">
            <h5 class="card-title text-warning">Verify your email</h5>
            <p class="text-muted">Please enter the OTP sent to your email <strong>{{ Auth::user()->email }}</strong>.</p>
            <form action="{{ route('verify.otp') }}" method="POST">
               @csrf
               <div class="mb-3">
                  <label for="otp" class="form-label">Enter OTP <em>(Check spam folder also)</em></label>
                  <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP" required>
               </div>
               <button type="submit" class="btn btn-success">Verify</button>
               <a href="/resend-otp">Send Code Again</a>
            </form>
            @if(session('error'))
            <div class="alert alert-danger mt-3">
               {{ session('error') }}
            </div>
            @endif
         </div>
      </div>
   </div>
   @elseif(Auth::user()->email_verified_at && (string)Auth::user()->email_verified_at == '2')
   <div class="mb-4">
      <div class="card">
         <div class="card-body">
            <h5 class="card-title">Your Account is suspended</h5>
         </div>
      </div>
   </div>
   @else
   {{-- চেক করুন এটা নিজের প্রোফাইল নাকি অন্যের প্রোফাইল --}}
   @if(Auth::id() === $user->id)
   {{-- নিজের প্রোফাইল হলে পোস্ট ফর্ম দেখাবে --}}
   <div class="mb-4">
      <div class="card">
         <div class="card-body">
            <h5 class="card-title">Upload Image & Add Text</h5>
            <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
               @csrf
               {{-- Post Category Dropdown --}}
               <div class="mb-3">
                    <label for="category_name" class="form-label">Post Category <span class="text-danger">*</span></label>
                    <div style="position: relative;">
                        <input type="text" 
                                class="form-control" 
                                id="category_name" 
                                name="category_name" 
                                placeholder="Type to search categories..."
                                autocomplete="off"
                                required>
                        <input type="hidden" id="category_id" name="category_id" value="">
                        <div id="suggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; z-index: 1000; display: none;"></div>
                    </div>
                    @error('category_id')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
              </div>


              {{-- Title Field --}}
<div class="mb-3">
    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Enter product/service title..." value="{{ old('title') }}" required>
    @error('title')
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

{{-- Price Field --}}
<div class="mb-3">
    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
    <input type="number" class="form-control" id="price" name="price" placeholder="Enter price..." value="{{ old('price') }}" min="0" step="0.01" required>
    @error('price')
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>




              <script>
// Categories data from backend
const categories = @json($categories);
const categoryInput = document.getElementById('category_name');
const categoryIdInput = document.getElementById('category_id');
const suggestionsDiv = document.getElementById('suggestions');
let filteredCategories = [];

function showSuggestions(searchTerm) {
    if (searchTerm.length === 0) {
        suggestionsDiv.style.display = 'none';
        return;
    }

    filteredCategories = categories.filter(category =>
        category.category_name.toLowerCase().includes(searchTerm.toLowerCase())
    );

    if (filteredCategories.length === 0) {
        suggestionsDiv.innerHTML = '<div style="padding: 10px 15px; color: #6c757d;">No matching categories found. You can create a new one!</div>';
        suggestionsDiv.style.display = 'block';
        return;
    }

    const suggestionsHtml = filteredCategories.map(category => `
        <div style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0;"
             onclick="selectCategory(${category.id}, '${category.category_name}')"
             onmouseover="this.style.backgroundColor='#f8f9fa'"
             onmouseout="this.style.backgroundColor='white'">
            ${category.category_name} <small style="color: #6c757d;">(${category.cat_type})</small>
        </div>
    `).join('');

    suggestionsDiv.innerHTML = suggestionsHtml;
    suggestionsDiv.style.display = 'block';
}

function selectCategory(id, name) {
    categoryInput.value = name;
    categoryIdInput.value = id;
    suggestionsDiv.style.display = 'none';
    toggleSubmit();
}

categoryInput.addEventListener('input', function() {
    const searchValue = this.value.trim();
    
    if (searchValue.length > 0) {
        showSuggestions(searchValue);
        
        // Check if typed value exactly matches any existing category
        const exactMatch = categories.find(category => 
            category.category_name.toLowerCase() === searchValue.toLowerCase()
        );
        
        if (exactMatch) {
            categoryIdInput.value = exactMatch.id; // Set existing category ID
        } else {
            categoryIdInput.value = ''; // Clear category_id for new category
        }
    } else {
        suggestionsDiv.style.display = 'none';
        categoryIdInput.value = '';
    }
    
    toggleSubmit();
});

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#category_name') && !e.target.closest('#suggestions')) {
        suggestionsDiv.style.display = 'none';
    }
});

function toggleSubmit() {
    const titleInput = document.getElementById('title');
    const priceInput = document.getElementById('price');
    
    // Allow submission if all required fields are filled, regardless of category_id
    const hasRequiredFields = titleInput.value.trim() !== '' && 
                             priceInput.value.trim() !== '' && 
                             categoryInput.value.trim() !== ''; // Changed from categoryIdInput to categoryInput
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = !hasRequiredFields;
}

// Event listeners
const titleInput = document.getElementById('title');
const priceInput = document.getElementById('price');

titleInput.addEventListener('input', toggleSubmit);
priceInput.addEventListener('input', toggleSubmit);
</script>


               <div class="mb-3">
                  <label for="image" class="form-label">Choose Image</label>
                  <input class="form-control" type="file" id="image" name="image" accept="image/*">
                  @error('image')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
               </div>
               <div class="mb-3">
                  <label for="description" class="form-label">Product or Service Description</label>
                  <textarea class="form-control" id="description" name="description" rows="4" placeholder="Type your text here...">{{ old('description') }}</textarea>
                  @error('description')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
               </div>
               <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Add</button>
               <script>
                  const imageInput = document.getElementById('image');
                  const descInput = document.getElementById('description');
                  const categorySelect = document.getElementById('category_id');
                  const submitBtn = document.getElementById('submitBtn');
                  
                  function toggleSubmit() {
                      if((imageInput.files.length > 0 || descInput.value.trim() !== '') && categorySelect.value !== '') {
                          submitBtn.disabled = false; // active
                      } else {
                          submitBtn.disabled = true; // inactive
                      }
                  }
                  
                  imageInput.addEventListener('change', toggleSubmit);
                  descInput.addEventListener('input', toggleSubmit);
                  categorySelect.addEventListener('change', toggleSubmit);
               </script>
            </form>
            @if(session('success'))
            <div class="alert alert-success mt-3">
               {{ session('success') }}
            </div>
            @endif
         </div>
      </div>
   </div>
   @else
   {{-- অন্যের প্রোফাইল হলে প্রোফাইল ইনফরমেশন দেখাবে --}}
   <div class="">
      <div class="col-12">
         <div class="card mb-4">
            <div class="card-body text-center">
               <img src="{{ $user->image ? asset('profile-image/'.$user->image) : 'https://cdn-icons-png.flaticon.com/512/219/219983.png' }}"
                  class="rounded-circle mb-3"
                  alt="Profile Photo"
                  style="width:100px; height:100px; object-fit:cover;">
               <h4 class="mb-1">{{ $user->name }}</h4>
               <p class="text-muted mb-2">{{ '@' . $user->username }}</p>
               @if($user->email)
               <p class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
               @endif
               @if($user->location)
               <p class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $user->location }}</p>
               @endif
               <!-- Post Count -->
               <div class="row text-center mt-3">
                  <div class="col">
                     <h5 class="mb-0">{{ $posts->total() }}</h5>
                     <small class="text-muted">Posts</small>
                  </div>
               </div>
               {{-- Follow/Unfollow Button (Optional) --}}
               <div class="mt-3">
                  <button class="btn btn-primary btn-sm">
                  <i class="bi bi-person-plus me-1"></i> Follow
                  </button>
                  <button class="btn btn-outline-secondary btn-sm ms-2">
                  <i class="bi bi-envelope me-1"></i> Message
                  </button>
               </div>
            </div>
         </div>
      </div>
   </div>
   @endif
   @endif
   @endauth
   @guest
   {{-- Guest user এর জন্য প্রোফাইল ইনফরমেশন দেখাবে --}}
   <div class="">
      <div class="col-12">
         <div class="card mb-4">
            <div class="card-body text-center">
               <img src="{{ $user->image ? asset('profile-image/'.$user->image) : 'https://cdn-icons-png.flaticon.com/512/219/219983.png' }}"
                  class="rounded-circle mb-3"
                  alt="Profile Photo"
                  style="width:100px; height:100px; object-fit:cover;">
               <h4 class="mb-1">{{ $user->name }}</h4>
               <p class="text-muted mb-2">{{ '@' . $user->username }}</p>
               @if($user->email)
               <p class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
               @endif
               @if($user->location)
               <p class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $user->location }}</p>
               @endif
               <!-- Post Count -->
               <div class=" text-center mt-3">
                  <div class="col">
                     <h5 class="mb-0">{{ $posts->total() }}</h5>
                     <small class="text-muted">Posts</small>
                  </div>
               </div>
               <div class="alert alert-info mt-3">
                  Please <a href="{{ route('login') }}">login</a> to interact with posts.
               </div>
            </div>
         </div>
      </div>
   </div>
   @endguest

   

   @include('frontend.products-partial')


   
</div>
{{-- User-specific Posts Loading JavaScript --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
       let currentPage = 1;
       let isLoading = false;
       
       // User ID get করুন (PHP থেকে)
       const userId = @json($user->id ?? null);
       
       const postsContainer = document.getElementById('posts-container');
       const loadingSpinner = document.getElementById('loading');
       const loadMoreBtn = document.getElementById('load-more-btn');
       const loadMoreContainer = document.getElementById('load-more-container');
   
       // Load More Button Click
       if (loadMoreBtn && userId) {
           loadMoreBtn.addEventListener('click', function() {
               loadMorePosts();
           });
       }
   
       // Auto Load on Scroll (Optional)
       window.addEventListener('scroll', function() {
           if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
               if (!isLoading && loadMoreBtn && loadMoreBtn.style.display !== 'none' && userId) {
                   loadMorePosts();
               }
           }
       });
   
       function loadMorePosts() {
           if (isLoading || !userId) return;
           
           isLoading = true;
           currentPage++;
           
           // Show loading spinner
           loadingSpinner.style.display = 'block';
           if (loadMoreBtn) loadMoreBtn.style.display = 'none';
           
           // User-specific route ব্যবহার করুন
           const url = `/posts/load-more/${userId}`;
           
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
                   
                   // Initialize read more functionality for new posts
                   initReadMore();
                   
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
       
       // Initialize Read More functionality
       function initReadMore() {
           document.querySelectorAll('.read-more').forEach(link => {
               // Remove existing event listeners to avoid duplicates
               link.replaceWith(link.cloneNode(true));
           });
           
           document.querySelectorAll('.read-more').forEach(link => {
               link.addEventListener('click', function() {
                   const para = this.previousElementSibling;
                   if (para.style.maxHeight === 'none') {
                       para.style.maxHeight = '75px';
                       this.textContent = 'Read more';
                   } else {
                       para.style.maxHeight = 'none';
                       this.textContent = 'Read less';
                   }
               });
           });
       }
       
       // Initialize read more for existing posts
       initReadMore();
   });
</script>
@endsection
