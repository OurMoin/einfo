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
      <img src="https://einfo.site/logo.png"
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







<!-- Floating Cart Icon -->
<div class="floating-cart" id="floatingCart">
    <div class="cart-icon">
        <i class="bi bi-cart3"></i>
        <span class="cart-badge" id="cartBadge">0</span>
    </div>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cartItems">
                    <div class="text-center text-muted" id="emptyCart">
                        <i class="bi bi-cart-x fs-1"></i>
                        <p>Your cart is empty</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total: ৳<span id="cartTotal">0</span></strong>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="orderBtn" onclick="showOrderForm()">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Form Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="orderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control" id="customerPhone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shipping Address *</label>
                        <textarea class="form-control" id="customerAddress" rows="3" required></textarea>
                    </div>
                    <div class="border p-3 bg-light">
                        <h6>Order Summary:</h6>
                        <div id="orderSummary"></div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total: ৳<span id="orderTotal">0</span></strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="submitOrderBtn">
                        Confirm Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSS Styles -->
<style>
/* Floating Cart Styles */
.floating-cart {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    cursor: pointer;
}

.cart-icon {
    background: #007bff;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
    position: relative;
}

.cart-icon:hover {
    background: #0056b3;
    transform: scale(1.1);
}

.cart-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.cart-badge.hidden {
    display: none;
}

/* Cart Item Styles */
.cart-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
}

.cart-item-info {
    flex: 1;
}

.cart-item-title {
    font-weight: 600;
    margin-bottom: 5px;
}

.cart-item-price {
    color: #007bff;
    font-weight: 600;
}

.quantity-controls {
    display: flex;
    align-items: center;
    margin: 10px 0;
}

.quantity-btn {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 4px;
}

.quantity-btn:hover {
    background: #e9ecef;
}

.quantity-input {
    width: 50px;
    text-align: center;
    border: 1px solid #dee2e6;
    border-left: none;
    border-right: none;
    height: 30px;
}

.remove-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
}

.remove-btn:hover {
    background: #c82333;
}

/* Animation */
@keyframes bounceIn {
    0% { transform: scale(0.3) rotate(180deg); opacity: 0; }
    50% { transform: scale(1.05) rotate(0deg); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}

.cart-animate {
    animation: bounceIn 0.6s ease;
}
</style>

<!-- JavaScript Cart System -->
<script>
class CartSystem {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('shopping_cart')) || [];
        this.init();
    }

    init() {
        this.updateCartBadge();
        this.bindEvents();
    }

    bindEvents() {
        // Floating cart click
        document.getElementById('floatingCart').addEventListener('click', () => {
            this.showCartModal();
        });

        // Order form submit
        document.getElementById('orderForm').addEventListener('submit', (e) => {
            this.submitOrder(e);
        });
    }

    addToCart(productId, productName, productPrice, productImage) {
        const existingItem = this.cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.cart.push({
                id: productId,
                name: productName,
                price: parseFloat(productPrice),
                image: productImage,
                quantity: 1
            });
        }
        
        this.saveCart();
        this.updateCartBadge();
        this.showAddedAnimation();
        this.showToast(`${productName} added to cart!`, 'success');
    }

    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        this.updateCartBadge();
        this.updateCartModal();
    }

    updateQuantity(productId, newQuantity) {
        if (newQuantity <= 0) {
            this.removeFromCart(productId);
            return;
        }
        
        const item = this.cart.find(item => item.id === productId);
        if (item) {
            item.quantity = parseInt(newQuantity);
            this.saveCart();
            this.updateCartModal();
        }
    }

    saveCart() {
        localStorage.setItem('shopping_cart', JSON.stringify(this.cart));
    }

    updateCartBadge() {
        const badge = document.getElementById('cartBadge');
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        
        badge.textContent = totalItems;
        badge.classList.toggle('hidden', totalItems === 0);
    }

    showCartModal() {
        this.updateCartModal();
        new bootstrap.Modal(document.getElementById('cartModal')).show();
    }

    updateCartModal() {
        const cartItems = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        const cartTotal = document.getElementById('cartTotal');
        
        if (this.cart.length === 0) {
            emptyCart.style.display = 'block';
            cartTotal.textContent = '0';
            return;
        }
        
        emptyCart.style.display = 'none';
        
        let html = '';
        let total = 0;
        
        this.cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            html += `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">৳${item.price}</div>
                        <div class="quantity-controls">
                            <button class="quantity-btn" onclick="cart.updateQuantity('${item.id}', ${item.quantity - 1})">-</button>
                            <input type="number" class="quantity-input" value="${item.quantity}" 
                                   onchange="cart.updateQuantity('${item.id}', this.value)">
                            <button class="quantity-btn" onclick="cart.updateQuantity('${item.id}', ${item.quantity + 1})">+</button>
                        </div>
                    </div>
                    <div>
                        <div class="text-end mb-2">৳${itemTotal}</div>
                        <button class="remove-btn" onclick="cart.removeFromCart('${item.id}')">Remove</button>
                    </div>
                </div>
            `;
        });
        
        cartItems.innerHTML = html;
        cartTotal.textContent = total.toFixed(2);
    }

    showAddedAnimation() {
        const cartIcon = document.querySelector('.cart-icon');
        cartIcon.classList.add('cart-animate');
        setTimeout(() => {
            cartIcon.classList.remove('cart-animate');
        }, 600);
    }

    showToast(message, type = 'success') {
        // Toast notification function (reuse from your existing code)
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    async submitOrder(e) {
        e.preventDefault();
        
        if (this.cart.length === 0) {
            this.showToast('Your cart is empty!', 'error');
            return;
        }
        
        const phone = document.getElementById('customerPhone').value;
        const address = document.getElementById('customerAddress').value;
        const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        const submitBtn = document.getElementById('submitOrderBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Placing Order...';
        
        try {
            const response = await fetch('{{ route("orders.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone: phone,
                    shipping_address: address,
                    total_amount: total,
                    cart_items: this.cart
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.cart = [];
                this.saveCart();
                this.updateCartBadge();
                
                // Close modals
                bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
                bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
                
                this.showToast('Order placed successfully!', 'success');
                
                // Reset form
                document.getElementById('orderForm').reset();
            } else {
                this.showToast(data.message || 'Order failed!', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('Something went wrong!', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Confirm Order';
        }
    }
}

// Initialize cart system
const cart = new CartSystem();

// Global functions
function showOrderForm() {
    if (cart.cart.length === 0) {
        cart.showToast('Your cart is empty!', 'error');
        return;
    }
    
    // Update order summary
    const orderSummary = document.getElementById('orderSummary');
    const orderTotal = document.getElementById('orderTotal');
    
    let html = '';
    let total = 0;
    
    cart.cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        html += `
            <div class="d-flex justify-content-between">
                <span>${item.name} x ${item.quantity}</span>
                <span>৳${itemTotal}</span>
            </div>
        `;
    });
    
    orderSummary.innerHTML = html;
    orderTotal.textContent = total.toFixed(2);
    
    // Hide cart modal and show order modal
    bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
    new bootstrap.Modal(document.getElementById('orderModal')).show();
}

// Add to cart function for product cards
function addToCart(productId, productName, productPrice, productImage) {
    cart.addToCart(productId, productName, productPrice, productImage);
}
</script>





@yield('main-content')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>