<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Debug: Log incoming request
        \Log::info('Order Request Data:', $request->all());
        
        $request->validate([
            'phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'total_amount' => 'required|numeric|min:0',
            'cart_items' => 'required|array|min:1'
        ]);

        try {
            \Log::info('Validation passed, processing order...');
            
            // Group cart items by vendor
            $vendorGroups = collect($request->cart_items)->groupBy(function ($item) {
                $post = Post::find($item['id']);
                \Log::info('Post found for ID ' . $item['id'] . ': ' . ($post ? 'Yes' : 'No'));
                return $post ? $post->user_id : null;
            });

            \Log::info('Vendor Groups:', $vendorGroups->toArray());

            $createdOrders = [];

            foreach ($vendorGroups as $vendorId => $items) {
                if (!$vendorId) {
                    \Log::warning('Skipping vendor group with null vendor_id');
                    continue;
                }

                \Log::info('Processing vendor ID: ' . $vendorId);

                // Calculate total for this vendor
                $vendorTotal = $items->sum(function ($item) {
                    return $item['price'] * $item['quantity'];
                });

                // Prepare post_ids with quantities and service_time
                $postIds = $items->map(function ($item) {
                    $data = [
                        'post_id' => (int) $item['id'],
                        'quantity' => (int) $item['quantity']
                    ];
                   
                    // Add service_time if exists
                    if (isset($item['service_time'])) {
                        $data['service_time'] = $item['service_time'];
                    }
                   
                    return $data;
                })->toArray();

                \Log::info('Order data to be created:', [
                    'user_id' => auth()->id(),
                    'vendor_id' => $vendorId,
                    'phone' => $request->phone,
                    'shipping_address' => $request->shipping_address,
                    'total_amount' => $vendorTotal,
                    'status' => 'pending',
                    'post_ids' => $postIds
                ]);

                // Create order for this vendor
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'vendor_id' => $vendorId,
                    'phone' => $request->phone,
                    'shipping_address' => $request->shipping_address,
                    'total_amount' => $vendorTotal,
                    'status' => 'pending',
                    'post_ids' => $postIds
                ]);

                \Log::info('Order created with ID: ' . $order->id);

                $createdOrders[] = [
                    'order_id' => $order->id,
                    'vendor_id' => $vendorId,
                    'total' => $vendorTotal,
                    'items_count' => count($postIds)
                ];
            }

            \Log::info('All orders created successfully:', $createdOrders);

            return response()->json([
                'success' => true,
                'message' => 'Orders placed successfully!',
                'orders' => $createdOrders,
                'total_orders' => count($createdOrders)
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Order creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Buy page - যারা order দিয়েছে তারা দেখবে
    public function buyPage()
    {
        $user = Auth::user();
        
        // User যে orders দিয়েছে সেগুলো দেখাবে
        $orders = Order::where('user_id', $user->id)
            ->with(['vendor'])
            ->latest()
            ->paginate(10);

        return view('frontend.buy', compact('orders'));
    }

    // Sell page - যাদের কাছে order এসেছে তারা দেখবে
    public function sellPage()
    {
        $user = Auth::user();
        
        // User এর কাছে যে orders এসেছে সেগুলো দেখাবে
        $orders = Order::where('vendor_id', $user->id)
            ->with(['user'])
            ->latest()
            ->paginate(10);

        return view('frontend.sell', compact('orders'));
    }

    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['vendor'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'vendor'])->findOrFail($id);
        
        // Check if user is authorized to view this order
        if ($order->user_id !== auth()->id() && $order->vendor_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        
        // Only vendor can update order status
        if ($order->vendor_id !== auth()->id()) {
            abort(403);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);
    }
}