@extends('frontend.master')

@section('main-content')
<div class="container-fluid px-2 py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-shop me-2"></i>My Store Orders
                </h3>
                <div class="badge bg-success fs-6">
                    {{ $orders->total() }} Total Orders
                </div>
            </div>

            @if($orders->count() > 0)
                <!-- Order Status Filter -->
                <div class="mb-4">
                    <div class="btn-group" role="group" aria-label="Order Status Filter">
                        <button type="button" class="btn btn-outline-primary active" onclick="filterOrders('all')">All</button>
                        <button type="button" class="btn btn-outline-warning" onclick="filterOrders('pending')">Pending</button>
                        <button type="button" class="btn btn-outline-info" onclick="filterOrders('confirmed')">Confirmed</button>
                        <button type="button" class="btn btn-outline-primary" onclick="filterOrders('processing')">Processing</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="filterOrders('shipped')">Shipped</button>
                        <button type="button" class="btn btn-outline-success" onclick="filterOrders('delivered')">Delivered</button>
                    </div>
                </div>

                <div class="row">
                    @foreach($orders as $order)
                        <div class="col-12 mb-3 order-card" data-status="{{ $order->status }}">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Order #{{ $order->id }}</h6>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y - h:i A') }}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Status Update Dropdown -->
                                        <select class="form-select form-select-sm" onchange="updateOrderStatus({{ $order->id }}, this.value)" style="width: auto;">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        
                                        <span class="badge 
                                            @if($order->status == 'pending') bg-warning
                                            @elseif($order->status == 'confirmed') bg-info
                                            @elseif($order->status == 'processing') bg-primary
                                            @elseif($order->status == 'shipped') bg-secondary
                                            @elseif($order->status == 'delivered') bg-success
                                            @elseif($order->status == 'cancelled') bg-danger
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Customer Info -->
                                            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                                <img src="{{ asset('profile-image/' . ($order->user->image ?? 'default.png')) }}" 
                                                     alt="Customer" class="rounded-circle me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-1">{{ $order->user->name }}</h6>
                                                    <small class="text-muted">@einfo.{{ $order->user->username }}</small>
                                                    <div class="mt-1">
                                                        <i class="bi bi-telephone me-1"></i>{{ $order->phone }}
                                                    </div>
                                                    <div>
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $order->shipping_address }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Ordered Items -->
                                            <div>
                                                <h6>Ordered Items ({{ count($order->post_ids) }} products):</h6>
                                                @foreach($order->getOrderedPostsWithDetails() as $post)
                                                    <div class="d-flex align-items-center mb-2 p-2 border rounded">
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
                                                                Qty: {{ $post->ordered_quantity }} × {{ $post->price }}
                                                                @if($post->service_time)
                                                                    | Service: {{ $post->service_time }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <strong>{{ $post->price * $post->ordered_quantity }}</strong>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="mb-3">
                                                <h4 class="text-success mb-0">{{ number_format($order->total_amount, 2) }}</h4>
                                                <small class="text-muted">Total Amount</small>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>View Details
                                                </a>
                                                <a href="tel:{{ $order->phone }}" class="btn btn-outline-success btn-sm">
                                                    <i class="bi bi-telephone me-1"></i>Call Customer
                                                </a>
                                                @if($order->status == 'pending')
                                                    <button class="btn btn-success btn-sm" onclick="updateOrderStatus({{ $order->id }}, 'confirmed')">
                                                        <i class="bi bi-check-circle me-1"></i>Confirm Order
                                                    </button>
                                                @endif
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
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-shop-window" style="font-size: 4rem; color: #6c757d;"></i>
                    </div>
                    <h5 class="text-muted">No Orders Received Yet</h5>
                    <p class="text-muted">You haven't received any orders yet. Share your products to get more orders!</p>
                    <a href="/" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Add Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateOrderStatus(orderId, status) {
    fetch(`/orders/${orderId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the badge color
            const orderCard = document.querySelector(`[data-status]:has(select option[value="${status}"][selected])`).closest('.order-card');
            orderCard.setAttribute('data-status', status);
            
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'toast show position-fixed top-0 end-0 m-3';
            toast.innerHTML = `
                <div class="toast-body bg-success text-white">
                    <i class="bi bi-check-circle me-2"></i>Order status updated successfully!
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
            
            // Reload page to update badge colors
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function filterOrders(status) {
    const orderCards = document.querySelectorAll('.order-card');
    const filterButtons = document.querySelectorAll('.btn-group button');
    
    // Update active button
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Show/hide orders
    orderCards.forEach(card => {
        if (status === 'all' || card.getAttribute('data-status') === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection