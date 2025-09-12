@extends('frontend.master')

@section('main-content')
<div class="container-fluid px-2 py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-bag-check me-2"></i>My Orders
                </h3>
                <div class="badge bg-primary fs-6">
                    {{ $orders->total() }} Total Orders
                </div>
            </div>

            @if($orders->count() > 0)
                <div class="row">
                    @foreach($orders as $order)
                        <div class="col-12 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Order #{{ $order->id }}</h6>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y - h:i A') }}</small>
                                    </div>
                                    <div class="text-end">
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
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="{{ asset('profile-image/' . ($order->vendor->image ?? 'default.png')) }}" 
                                                     alt="Vendor" class="rounded-circle me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0">{{ $order->vendor->name }}</h6>
                                                    <small class="text-muted">@einfo.{{ $order->vendor->username }}</small>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Items:</strong> {{ count($order->post_ids) }} products
                                            </div>
                                            <div class="mb-2">
                                                <strong>Shipping Address:</strong> {{ $order->shipping_address }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Phone:</strong> {{ $order->phone }}
                                            </div>
                                            
                                            <!-- Ordered Items -->
                                            <div class="mt-3">
                                                <h6>Ordered Items:</h6>
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
                                                                Qty: {{ $post->ordered_quantity }} × ৳{{ $post->price }}
                                                                @if($post->service_time)
                                                                    | Service: {{ $post->service_time }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <strong>৳{{ $post->price * $post->ordered_quantity }}</strong>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="mb-3">
                                                <h4 class="text-primary mb-0">৳{{ number_format($order->total_amount, 2) }}</h4>
                                                <small class="text-muted">Total Amount</small>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>View Details
                                                </a>
                                                @if($order->status == 'pending')
                                                    <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder({{ $order->id }})">
                                                        <i class="bi bi-x-circle me-1"></i>Cancel Order
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
                        <i class="bi bi-bag-x" style="font-size: 4rem; color: #6c757d;"></i>
                    </div>
                    <h5 class="text-muted">No Orders Yet</h5>
                    <p class="text-muted">You haven't placed any orders. Start shopping to see your orders here!</p>
                    <a href="/" class="btn btn-primary">
                        <i class="bi bi-shop me-2"></i>Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch(`/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: 'cancelled' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to cancel order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
@endsection