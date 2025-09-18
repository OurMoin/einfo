@extends('frontend.master')

@section('main-content')
<div class="container container-fluid px-2 py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-truck me-2"></i>Delivery Orders
                </h3>
                <div class="badge bg-info fs-6" id="orderCount">
                    {{ $orders->total() }} Confirmed Orders
                </div>
            </div>

            @if($orders->count() > 0)
                <div class="row">
                    @foreach($orders as $order)
                        <div class="col-12 mb-4 order-card" data-order-id="{{ $order->id }}">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-info bg-opacity-10 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Order #{{ $order->id }}</h6>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y - h:i A') }}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-info">
                                            <i class="bi bi-check-circle me-1"></i>Confirmed
                                        </span>
                                        <span class="badge bg-success">
                                            ৳{{ number_format($order->total_amount, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Customer Info -->
                                            <div class="mb-3">
                                                <h6 class="text-muted mb-2">
                                                    <i class="bi bi-person-circle me-2"></i>Customer Information
                                                </h6>
                                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                                    <img src="{{ asset('profile-image/' . ($order->user->image ?? 'default.png')) }}" 
                                                         alt="Customer" class="rounded-circle me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $order->user->name }}</h6>
                                                        <div class="small text-muted">
                                                            <div>
                                                                <i class="bi bi-telephone-fill me-2"></i>
                                                                <strong>{{ $order->phone }}</strong>
                                                            </div>
                                                            <div class="mt-1">
                                                                <i class="bi bi-geo-alt-fill me-2"></i>
                                                                {{ $order->shipping_address }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Vendor Info -->
                                            <div class="mb-3">
                                                <h6 class="text-muted mb-2">
                                                    <i class="bi bi-shop me-2"></i>Vendor Information
                                                </h6>
                                                <div class="p-3 bg-light rounded">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('profile-image/' . ($order->vendor->image ?? 'default.png')) }}" 
                                                             alt="Vendor" class="rounded-circle me-3" 
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                        <div>
                                                            <h6 class="mb-0">{{ $order->vendor->name }}</h6>
                                                            <small class="text-muted">@{{ $order->vendor->username }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Ordered Items -->
                                            <div>
                                                <h6 class="text-muted mb-2">
                                                    <i class="bi bi-box-seam me-2"></i>Order Items ({{ count($order->post_ids) }} products)
                                                </h6>
                                                <div class="border rounded p-2">
                                                    @foreach($order->getOrderedPostsWithDetails() as $post)
                                                        <div class="d-flex align-items-center mb-2 p-2 bg-white rounded">
                                                            @if($post->images && count(json_decode($post->images)) > 0)
                                                                @php $images = json_decode($post->images); @endphp
                                                                <img src="{{ asset('post-images/' . $images[0]) }}" 
                                                                     alt="Product" class="me-3 rounded" 
                                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-light me-3 rounded d-flex align-items-center justify-content-center" 
                                                                     style="width: 50px; height: 50px;">
                                                                    <i class="bi bi-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0">{{ $post->name }}</h6>
                                                                <small class="text-muted">
                                                                    Qty: <strong>{{ $post->ordered_quantity }}</strong> × ৳{{ $post->price }}
                                                                    @if($post->service_time)
                                                                        | Service: {{ $post->service_time }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                            <div class="text-end">
                                                                <strong class="text-success">৳{{ $post->price * $post->ordered_quantity }}</strong>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <!-- Order Summary -->
                                            <div class="bg-light rounded p-3 mb-3">
                                                <h5 class="mb-3">Order Summary</h5>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Subtotal:</span>
                                                    <strong>৳{{ number_format($order->total_amount, 2) }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Delivery Fee:</span>
                                                    <strong>৳0.00</strong>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span class="h6">Total:</span>
                                                    <strong class="h5 text-success">৳{{ number_format($order->total_amount, 2) }}</strong>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-success btn-lg accept-order-btn" 
                                                        onclick="acceptOrder({{ $order->id }})"
                                                        data-order-id="{{ $order->id }}">
                                                    <i class="bi bi-check2-circle me-2"></i>Accept Order
                                                </button>
                                                <a href="tel:{{ $order->phone }}" class="btn btn-outline-primary">
                                                    <i class="bi bi-telephone-fill me-2"></i>Call Customer
                                                </a>
                                                <a href="tel:{{ $order->vendor->phone ?? '' }}" class="btn btn-outline-secondary">
                                                    <i class="bi bi-telephone me-2"></i>Call Vendor
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-truck" style="font-size: 4rem; color: #6c757d;"></i>
                    </div>
                    <h5 class="text-muted">No Confirmed Orders Available</h5>
                    <p class="text-muted">There are no confirmed orders ready for delivery at this moment.</p>
                    <button class="btn btn-primary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .order-card {
        transition: all 0.3s ease;
    }
    
    .order-card.accepted {
        opacity: 0.5;
        pointer-events: none;
    }
    
    .accept-order-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .accept-order-btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .badge {
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: start !important;
            gap: 10px;
        }
    }
</style>

<script>
function acceptOrder(orderId) {
    const button = event.target;
    const card = document.querySelector(`.order-card[data-order-id="${orderId}"]`);
    
    // Confirmation dialog
    if (!confirm('Are you sure you want to accept this order for delivery?')) {
        return;
    }
    
    // Show loading state
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Accepting...';
    button.disabled = true;
    
    // Send request to accept order
    fetch(`/orders/${orderId}/accept-delivery`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Order Accepted!';
            button.classList.remove('btn-success');
            button.classList.add('btn-secondary');
            
            // Add accepted class to card
            card.classList.add('accepted');
            
            // Show success notification
            showNotification('success', 'Order accepted successfully! The order is now being processed.');
            
            // Remove the card after 2 seconds
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.transform = 'translateX(100%)';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    updateOrderCount();
                }, 500);
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to accept order');
        }
    })
    .catch(error => {
        // Restore button state
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Show error message
        showNotification('error', error.message || 'Failed to accept order. Please try again.');
    });
}

function updateOrderCount() {
    const orderCards = document.querySelectorAll('.order-card:not(.accepted)');
    const orderCountBadge = document.getElementById('orderCount');
    orderCountBadge.textContent = `${orderCards.length} Confirmed Orders`;
}

function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed top-0 start-50 translate-middle-x mt-3`;
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Auto-refresh page every 30 seconds to check for new orders
setInterval(() => {
    if (document.querySelectorAll('.order-card:not(.accepted)').length === 0) {
        location.reload();
    }
}, 30000);
</script>
@endsection