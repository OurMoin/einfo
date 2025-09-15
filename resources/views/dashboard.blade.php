@extends("frontend.master")
@section('main-content')
<div class="container mt-4">
<!-- Dashboard Content -->
<div class="row mt-3">
   @auth
   @if(is_null(Auth::user()->email_verified) || (Auth::user()->email_verified > 0 && Auth::user()->email_verified < 9))
   {{-- যদি ইমেইল ভেরিফাই না করা থাকে অথবা email_verified count 1-8 এর মধ্যে থাকে --}}
   <div class="mb-4">
      <div class="card border-warning">
         <div class="card-body">
            <h5 class="card-title text-warning">Verify your email</h5>
            <p class="text-muted">Please enter the OTP sent to your email <strong>{{ Auth::user()->email }}</strong>.</p>
            @if(Auth::user()->email_verified && Auth::user()->email_verified > 0 && Auth::user()->email_verified < 9)
            <p class="text-info">OTP attempts: {{ Auth::user()->email_verified }}/9</p>
            @endif
            <form action="{{ route('verify.otp') }}" method="POST">
               @csrf
               <div class="mb-3">
                  <label for="otp" class="form-label">Enter OTP <em>(Check spam folder also)</em></label>
                  <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP" required>
               </div>
               <button type="submit" class="btn btn-success">Verify</button>
               @if(Auth::user()->email_verified < 9)
               <a href="/resend-otp">Send Code Again</a>
               @endif
            </form>
            @if(session('error'))
            <div class="alert alert-danger mt-3">
               {{ session('error') }}
            </div>
            @endif
         </div>
      </div>
   </div>
   @elseif(Auth::user()->email_verified == 9)
   {{-- যদি email_verified 9 হয় (suspended) --}}
   <div class="mb-4">
      <div class="card border-danger">
         <div class="card-body">
            <h5 class="card-title text-danger">Your Account is suspended</h5>
            <p class="text-muted">You have exceeded the maximum OTP attempts. Please contact support or try with a different email.</p>
         </div>
      </div>
   </div>
   @else
   {{-- email_verified == 0 মানে verified --}}
   
   {{-- Profile Information Card (Always Show) --}}
   <div class="">
      <div class="col-12">
         <div class="card mb-4">
            <div class="card-body text-center">
               <img src="{{ $user->image ? asset('profile-image/'.$user->image) : 'https://cdn-icons-png.flaticon.com/512/219/219983.png' }}"
                  class="rounded-circle mb-3"
                  alt="Profile Photo"
                  style="width:100px; height:100px; object-fit:cover;">
               <h4 class="mb-2">{{ $user->name }}</h4>
               <!-- <p class="text-muted mb-2">{{ '@' . $user->username }}</p> -->
               @if($user->category)
               <p class="text-muted mb-2">
                  <i class="bi bi-grid me-1"></i>{{ $user->category->category_name }}
               </p>
               @else
               <p class="text-muted">
                  <i class="bi bi-grid me-1"></i>{{ $user->job_title }}
               </p>
               @endif
               @if($user->area)
               <p class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $user->area }}</p>
               @endif
               <!-- Post Count -->
               <div class="row text-center mt-3">
                    <div class="col border-end">
                        <h5 class="mb-0">{{ $posts->total() }}</h5>
                        <small class="text-muted">Posts</small>
                    </div>
                    <div class="col border-end text-center">
                        <h5 class="mb-0" id="followersCount-{{ $user->id }}">{{ $user->followers()->count() ?? 0 }}</h5>
                        <small class="text-muted">Followers</small>
                    </div>
                    <div class="col">
                        <h5 class="mb-0">{{ $ratings ?? 0 }}/5</h5>
                        <small class="text-muted">Ratings</small>
                    </div>
                </div>

               
               {{-- Action Buttons Based on User Type --}}
               <div class="mt-3">
                  @if(Auth::id() === $user->id)
                  {{-- Own Profile - Show Add Post + Message Buttons (Same as Follow + Message) --}}
                  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPostModal">
                     <i class="bi bi-plus-circle me-1"></i> Add Post
                  </button>                  
                  @else
                  {{-- Other's Profile - Show Follow/Message Buttons --}}
                
                    @php
                        $isFollowing = auth()->user() && auth()->user()->following->contains($user->id);
                    @endphp

                    <button id="followBtn-{{ $user->id }}" class="btn btn-{{ $isFollowing ? 'danger' : 'primary' }} btn-sm"
                        onclick="toggleFollow({{ $user->id }})">
                        <i class="bi {{ $isFollowing ? 'bi-person-dash' : 'bi-person-plus' }} me-1"></i>
                        {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                    </button>


                <script>
function toggleFollow(userId) {
    let btn = document.getElementById('followBtn-' + userId);
    let followersCountEl = document.getElementById('followersCount-' + userId);

    let isFollowing = btn.classList.contains('btn-danger'); // check current state
    let url = isFollowing ? '/unfollow/' + userId : '/follow/' + userId;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Toggle button color
            btn.classList.toggle('btn-primary');
            btn.classList.toggle('btn-danger');

            // Toggle icon
            let icon = btn.querySelector('i');
            icon.classList.toggle('bi-person-plus');
            icon.classList.toggle('bi-person-dash');

            // Toggle text
            btn.textContent = isFollowing ? 'Follow' : 'Unfollow';
            btn.prepend(icon);

            // Update followers count instantly
            let count = parseInt(followersCountEl.textContent);
            followersCountEl.textContent = isFollowing ? count - 1 : count + 1;
        } else if(data.error) {
            alert(data.error);
        }
    })
    .catch(err => console.error(err));
}
</script>


                
                  @endif
                <button class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-telephone"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-envelope"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-globe"></i>
                </button>
                @php
                    // Current logged in user
                    $currentUserId = auth()->id();
                    // Profile owner
                    $profileUserId = $user->id;
                @endphp

                <button class="btn btn-outline-secondary btn-sm ms-2" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>

                <div class="dropdown">
                    
                    <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                        {{-- Share always দেখাবে --}}
                        <li>
                        <a class="dropdown-item" href="#" onclick="copyProfileLink(event)">Share</a>
                        </li>

                        <script>
                        function copyProfileLink(e) {
                            e.preventDefault(); // লিঙ্ককে redirect হতে দেয় না

                            // ধরুন profile link variable
                            let profileLink = window.location.href; // যদি বর্তমান পেজের URL হয়
                            // অথবা যদি user এর ID থাকে, তাহলে:
                            // let profileLink = `https://example.com/profile/${userId}`;

                            // Copy করতে Clipboard API ব্যবহার
                            navigator.clipboard.writeText(profileLink)
                            .then(() => {
                                alert("Profile link copied to clipboard!"); // success message
                            })
                            .catch(err => {
                                alert("Failed to copy link");
                                console.error(err);
                            });
                        }
                        </script>



                        {{-- Report/Block শুধু অন্যের profile এ দেখাবে --}}
                        @if($currentUserId !== $profileUserId)
                            <li><a class="dropdown-item" href="#">Report</a></li>
                            <li><a class="dropdown-item text-danger" href="#">Block</a></li>
                        @endif
                    </ul>
                </div>

               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- Create Post Modal (Only for Own Profile) --}}
   @if(Auth::id() === $user->id)
   <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="createPostModalLabel">Create New Post</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
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
               </form>
               @if(session('success'))
               <div class="alert alert-success mt-3">
                  {{ session('success') }}
               </div>
               @endif
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
               <button type="submit" form="createPostForm" class="btn btn-primary" id="submitBtn" disabled>Create Post</button>
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
               <h4 class="mb-2">{{ $user->name }}</h4>
               <!-- <p class="text-muted mb-2">{{ '@' . $user->username }}</p> -->
               @if($user->category)
               <p class="text-muted mb-2">
                  <i class="bi bi-grid me-1"></i>{{ $user->category->category_name }}
               </p>
               @else
               <p class="text-muted">
                  <i class="bi bi-grid me-1"></i>{{ $user->job_title }}
               </p>
               @endif
               @if($user->area)
               <p class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $user->area }}</p>
               @endif
               <!-- Post Count -->
               <div class="row text-center mt-3">
                    <div class="col border-end">
                        <h5 class="mb-0">{{ $posts->total() }}</h5>
                        <small class="text-muted">Posts</small>
                    </div>
                    <div class="col border-end text-center">
                        <h5 class="mb-0" id="followersCount-{{ $user->id }}">{{ $user->followers()->count() ?? 0 }}</h5>
                        <small class="text-muted">Followers</small>
                    </div>
                    <div class="col">
                        <h5 class="mb-0">{{ $ratings ?? 0 }}/5</h5>
                        <small class="text-muted">Ratings</small>
                    </div>
                </div>

               
               {{-- Action Buttons Based on User Type --}}
               <div class="mt-3">
                  
                <button class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-telephone"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-envelope"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-globe"></i>
                </button>

                <button class="btn btn-outline-secondary btn-sm ms-2" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>


                    <div class="alert alert-info mt-3 mb-0">
                     Please <a href="{{ route('register') }}">register</a> or <a href="{{ route('login') }}">login</a> to interact with posts.
                  </div>

                <div class="dropdown">
                    
                    <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                        {{-- Share always দেখাবে --}}
                        <li>
  <a class="dropdown-item" href="#" onclick="copyProfileLink(event)">Share</a>
</li>

<!-- Toast message div -->
<div id="toast" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #333;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    display: none;
    z-index: 9999;
">
  Profile link copied!
</div>

<script>
function copyProfileLink(e) {
    e.preventDefault();

    let profileLink = window.location.href;

    navigator.clipboard.writeText(profileLink)
      .then(() => {
        let toast = document.getElementById('toast');
        toast.style.display = 'block';
        // 2 সেকেন্ড পরে অটো hide
        setTimeout(() => {
          toast.style.display = 'none';
        }, 2000);
      })
      .catch(err => {
        console.error("Failed to copy: ", err);
      });
}
</script>


                    </ul>
                </div>

               </div>
            </div>
         </div>
      </div>
   </div>
            
   @endguest
   
   @include('frontend.products-partial')
</div>

{{-- Modal and Category JavaScript --}}
<script>
   // Categories data from backend
   const categories = @json($categories ?? []);
   const categoryInput = document.getElementById('category_name');
   const categoryIdInput = document.getElementById('category_id');
   const suggestionsDiv = document.getElementById('suggestions');
   let filteredCategories = [];
   
   if (categoryInput) {
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
   }
   
   function toggleSubmit() {
       const titleInput = document.getElementById('title');
       const priceInput = document.getElementById('price');
       const imageInput = document.getElementById('image');
       const descInput = document.getElementById('description');
       const submitBtn = document.getElementById('submitBtn');
       
       if (titleInput && priceInput && categoryInput && submitBtn) {
           // Check if all required fields are filled
           const hasRequiredFields = titleInput.value.trim() !== '' && 
                                    priceInput.value.trim() !== '' && 
                                    categoryInput.value.trim() !== '';
           
           // Check if at least image or description is provided
           const hasContent = (imageInput && imageInput.files.length > 0) || 
                             (descInput && descInput.value.trim() !== '');
           
           submitBtn.disabled = !(hasRequiredFields && hasContent);
       }
   }
   
   // Event listeners for form validation
   document.addEventListener('DOMContentLoaded', function() {
       const titleInput = document.getElementById('title');
       const priceInput = document.getElementById('price');
       const imageInput = document.getElementById('image');
       const descInput = document.getElementById('description');
       
       if (titleInput) titleInput.addEventListener('input', toggleSubmit);
       if (priceInput) priceInput.addEventListener('input', toggleSubmit);
       if (imageInput) imageInput.addEventListener('change', toggleSubmit);
       if (descInput) descInput.addEventListener('input', toggleSubmit);
       
       // Initial check
       toggleSubmit();
   });
</script>

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