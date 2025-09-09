<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    

    <style>
   .card-img-top {
   height: 100px;
   object-fit: cover;
   width: 100%;
   }
   .card-title {
   display: inline-block; /* অথবা block */
   width: 100%;       /* চাইলে fixed width দিতে পারো যেমন 200px */
   white-space: nowrap;
   overflow: hidden;
   text-overflow: ellipsis;
   }
   .price-tag {
   display: inline-block; /* inline → inline-block */
   max-width: 100px;      /* অথবা চাইলে fixed width */
   white-space: nowrap;
   overflow: hidden;
   text-overflow: ellipsis;
   }
   a {
   text-decoration: none;
   }
   @media (max-width: 767.98px) {
   .card-title {
   font-size: 0.9rem; /* Small size for mobile */
   }
   .price-tag {
   font-size: 0.7rem;
   }
   }
   @media (max-width: 575.98px) {
   .card-title {
   font-size: 0.8rem; /* Extra small for very small devices */
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
   left: 10px; /* চাইলে right: 10px ও করতে পারো */
   padding: 4px;
   font-size: 0.6rem;
   z-index: 10;
   text-transform: uppercase;
   }

        /* Force dropdown to open on left side */
        .navbar .dropdown-menu {
            right: 0 !important;
            left: auto !important;
            transform: none !important;
        }

input {
    outline: none !important;
    box-shadow: none !important;
}

input:focus {
    outline: none !important;
    box-shadow: none !important;
    border-color: inherit !important;
}

/* Browser er default clear button hide করার জন্য */
input::-webkit-search-cancel-button,
input::-webkit-search-decoration {
    -webkit-appearance: none;
    appearance: none;
}

/* Firefox er clear button hide */
input::-moz-search-cancel-button {
    display: none;
}

/* IE/Edge er clear button hide */
input::-ms-clear {
    display: none;
}

        .navbar {
  --bs-navbar-padding-x: 0;
  --bs-navbar-padding-y: 0 !important;}

  .navbar-expand-lg .navbar-nav .nav-link {
    padding-right: 0;
    padding-left: 0;
  }

  .navbar-brand {
  margin-right: 0;
}
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom sticky-top">
  <div class="container-fluid d-flex align-items-center">
    <!-- Left: Logo -->
    <a class="navbar-brand" href="/">
      <img src="https://www.freepnglogos.com/uploads/w-logo/red-circle-w-letter-logos-get-wrapped-official-website-1.png"
           class="rounded-circle"
           alt="User"
           style="width:32px; height:32px; object-fit:cover;">
    </a>
    <!-- Center: Search -->
    <form class="flex-grow-1 mx-3 container" style="width:180px;" onsubmit="handleSearch(event)">
       <div class="position-relative w-100"> 
        <input id="searchInput" class="form-control text-center"
               type="search" placeholder="Search" aria-label="Search">
        <button type="submit" id="searchIcon" class="position-absolute end-0 top-50 translate-middle-y pe-3 border-0 bg-transparent" style="display:none;">
          <i class="bi bi-search"></i>
        </button>
       </div> 
    </form>
    <!-- Right: User / Guest Menu -->
    <ul class="navbar-nav">
      @auth
        <li class="nav-item dropdown">
          <a class="nav-link d-flex align-items-center" href="javascript:void(0)" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ asset('profile-image/' . (Auth::user()->image ?? 'default.png')) }}"
                 class="rounded-circle"
                 alt="User"
                 style="width:32px; height:32px; object-fit:cover;">
          </a>
          <ul class="dropdown-menu position-absolute" aria-labelledby="userDropdown" style="z-index:1050;">
            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Profile</a></li>
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Settings</a></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dropdown-item text-danger" type="submit">Logout</button>
              </form>
            </li>
          </ul>
        </li>
      @endauth
      @guest
        <li class="nav-item dropdown">
          <a class="nav-link" href="javascript:void(0)" id="guestDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ asset('profile-image/' . (Auth::user()->image ?? 'default.png')) }}"
                 class="rounded-circle"
                 alt="User"
                 style="width:32px; height:32px; object-fit:cover;">
          </a>
          <ul class="dropdown-menu position-absolute" aria-labelledby="guestDropdown" style="z-index:1050;">
            <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
            <li><a class="dropdown-item" href="{{ route('register') }}">Signup</a></li>
          </ul>
        </li>
      @endguest
    </ul>
  </div>
</nav>

<script>
  const searchInput = document.getElementById('searchInput');
  const searchIcon = document.getElementById('searchIcon');
  
  searchInput.addEventListener('input', () => {
    if(searchInput.value.length > 0){
      searchIcon.style.display = 'block';
      searchInput.classList.remove('text-center');
      searchInput.classList.add('text-start');
    } else {
      searchIcon.style.display = 'none';
      searchInput.classList.remove('text-start');
      searchInput.classList.add('text-center');
    }
  });

  // Search function
  function handleSearch(event) {
    event.preventDefault();
    const query = searchInput.value.trim();
    if (query) {
      // Add your search logic here
      console.log('Searching for:', query);
      // Example: window.location.href = '/search?q=' + encodeURIComponent(query);
    }
  }


//   let lastScrollTop = 0;
//   const navbar = document.querySelector('.navbar');

//   window.addEventListener('scroll', function() {
//       let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      
//       if (scrollTop > lastScrollTop) {
         
//           navbar.style.transform = 'translateY(-100%)';
//           navbar.style.transition = 'transform 0.3s ease-in-out';
//       } else {
         
//           navbar.style.transform = 'translateY(0)';
//           navbar.style.transition = 'transform 0.3s ease-in-out';
//       }
      
//       lastScrollTop = scrollTop;
//   });


</script>


<!-- CSS Code - master.blade.php er <head> section e rakhben -->
<style>
/* Universal Image Zoomer Styles */
.img-zoomer-container {
    position: relative;
    display: inline-block;
    cursor: zoom-in;
    transition: transform 0.2s ease;
}

.img-zoomer-container:hover {
    transform: scale(1.02);
}

#img-zoomer {
    cursor: zoom-in;
    transition: all 0.3s ease;
}


/* Zoom Modal Styles */
.zoom-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99999;
    cursor: zoom-out;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.zoom-modal.active {
    display: flex;
    opacity: 1;
}

.zoom-modal img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    transition: transform 0.3s ease;
    cursor: zoom-out;
}



/* Close Button */
.zoom-close {
    position: absolute;
    top: 0;
    right: 30px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 100000;
    transition: color 0.2s ease;
    user-select: none;
}

.zoom-close:hover {
    color: #ff4444;
}

/* Loading Animation */
.zoom-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255,255,255,0.3);
    border-top: 4px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .zoom-modal img {
        max-width: 100%;
        max-height: 100%;
    }
    
    .zoom-close {
        top: 10px;
        right: 15px;
        font-size: 30px;
    }
}
</style>

<!-- JavaScript Code - master.blade.php er closing </body> tag er age rakhben -->
<!-- JavaScript Code - master.blade.php er closing </body> tag er age rakhben -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let modal = null;
    let modalOpen = false;
    
    function createModal() {
        if (!modal) {
            modal = document.createElement('div');
            modal.className = 'zoom-modal';
            modal.innerHTML = `
                <span class="zoom-close">&times;</span>
                <div class="zoom-loading"></div>
                <img src="" alt="Zoomed">
            `;
            document.body.appendChild(modal);
        }
        return modal;
    }
    
    function openModal(imgSrc) {
        const m = createModal();
        const img = m.querySelector('img');
        const loading = m.querySelector('.zoom-loading');
        const closeBtn = m.querySelector('.zoom-close');
        
        modalOpen = true;
        window.location.hash = '#zoom';
        
        m.classList.add('active');
        loading.style.display = 'block';
        img.style.display = 'none';
        
        const tempImg = new Image();
        tempImg.onload = function() {
            img.src = this.src;
            loading.style.display = 'none';
            img.style.display = 'block';
        };
        tempImg.src = imgSrc;
        
        // Close button ONLY
        closeBtn.onclick = function() {
            closeModal();
        };
    }
    
    function closeModal() {
        if (modal) {
            modal.classList.remove('active');
            modalOpen = false;
            if (window.location.hash === '#zoom') {
                history.back();
            }
        }
    }
    
    // Initialize images with lazy loading support
    function initImage(img) {
        if (img.getAttribute('data-zoom-init')) return;
        img.setAttribute('data-zoom-init', 'true');
        
        img.onclick = function() {
            let src = this.src;
            // Check for lazy loading attributes
            if (this.getAttribute('data-src')) {
                src = this.getAttribute('data-src');
            } else if (this.getAttribute('data-lazy')) {
                src = this.getAttribute('data-lazy');
            }
            openModal(src);
        };
    }
    
    // Initialize existing images
    document.querySelectorAll('#img-zoomer').forEach(initImage);
    
    // Watch for new images (AJAX/dynamic content)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    if (node.id === 'img-zoomer') {
                        initImage(node);
                    }
                    node.querySelectorAll && node.querySelectorAll('#img-zoomer').forEach(initImage);
                }
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Back button
    window.addEventListener('hashchange', function() {
        if (modalOpen && window.location.hash !== '#zoom') {
            if (modal) {
                modal.classList.remove('active');
                modalOpen = false;
            }
        }
    });
    
    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalOpen) {
            closeModal();
        }
    });
});
</script>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePostModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this post? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Post</button>
            </div>
        </div>
    </div>
</div>




{{-- Lazy Loading JavaScript with Delete Functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let isLoading = false;
    let postIdToDelete = null;
    
    // Get user ID if we're on a profile page (PHP থেকে)
    const userId = @json($user->id ?? null);
    
    const postsContainer = document.getElementById('posts-container');
    const loadingSpinner = document.getElementById('loading');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadMoreContainer = document.getElementById('load-more-container');

    // Load More Button Click
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            loadMorePosts();
        });
    }

    // Auto Load on Scroll (Optional)
    window.addEventListener('scroll', function() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
            if (!isLoading && loadMoreBtn && loadMoreBtn.style.display !== 'none') {
                loadMorePosts();
            }
        }
    });

    function loadMorePosts() {
        if (isLoading) return;
        
        isLoading = true;
        currentPage++;
        
        // Show loading spinner
        loadingSpinner.style.display = 'block';
        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
        
        // Determine the correct URL based on context
        let url;
        if (userId) {
            // Profile page - load user-specific posts
            url = `/posts/load-more/${userId}`;
        } else {
            // Dashboard/Home page - load all posts
            url = '{{ route("posts.loadmore") }}';
        }
        
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
                
                // Initialize functionality for new posts
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
    
    // Initialize Read More functionality using event delegation
    function initReadMore() {
        // Remove existing delegated event listener if any
        document.removeEventListener('click', handleReadMoreClick);
        // Add delegated event listener to document
        document.addEventListener('click', handleReadMoreClick);
    }
    
    // Handle read more clicks (works for both existing and new posts)
    function handleReadMoreClick(e) {
        if (e.target.classList.contains('read-more')) {
            e.preventDefault();
            const readMoreBtn = e.target;
            const para = readMoreBtn.previousElementSibling;
            
            if (para.style.maxHeight === 'none') {
                para.style.maxHeight = '75px';
                readMoreBtn.textContent = 'Read more';
            } else {
                para.style.maxHeight = 'none';
                readMoreBtn.textContent = 'Read less';
            }
        }
    }
    
    // Initialize Delete Button functionality using event delegation
    function initDeleteButtons() {
        // Remove existing delegated event listener if any
        document.removeEventListener('click', handleDeleteClick);
        // Add delegated event listener to document
        document.addEventListener('click', handleDeleteClick);
    }
    
    // Handle delete button clicks (works for both existing and new posts)
    function handleDeleteClick(e) {
        if (e.target.closest('.delete-post-btn')) {
            e.preventDefault();
            const deleteBtn = e.target.closest('.delete-post-btn');
            postIdToDelete = deleteBtn.getAttribute('data-post-id');
        }
    }
    
    // Handle confirm delete
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (postIdToDelete) {
                deletePost(postIdToDelete);
            }
        });
    }
    
    // Delete post function
    function deletePost(postId) {
        // Show loading state
        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Deleting...';
        
        fetch(`/posts/${postId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the post from DOM
                const postElement = document.querySelector(`[data-post-id="${postId}"]`);
                if (postElement) {
                    postElement.remove();
                }
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deletePostModal'));
                modal.hide();
                
                // Show success message
                showToast('Post deleted successfully!', 'success');
            } else {
                showToast(data.message || 'Failed to delete post', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Something went wrong. Please try again.', 'error');
        })
        .finally(() => {
            // Reset button state
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.innerHTML = 'Delete Post';
            postIdToDelete = null;
        });
    }
    
    // Toast notification function
    function showToast(message, type = 'success') {
        // Create toast element
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Create or get toast container
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        // Add toast to container
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        // Initialize and show toast
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }
    
    // Initialize functionality for existing posts
    initReadMore();
    initDeleteButtons();
});
</script>
@yield('main-content')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>