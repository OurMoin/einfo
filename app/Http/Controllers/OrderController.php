<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Post;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'total_amount' => 'required|numeric|min:0',
            'cart_items' => 'required|array|min:1'
        ]);

        try {
            // Group cart items by vendor
            $vendorGroups = collect($request->cart_items)->groupBy(function ($item) {
                $post = Post::find($item['id']);
                return $post ? $post->user_id : null;
            });

            $createdOrders = [];

            foreach ($vendorGroups as $vendorId => $items) {
                if (!$vendorId) continue;

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

                $createdOrders[] = [
                    'order_id' => $order->id,
                    'vendor_id' => $vendorId,
                    'total' => $vendorTotal,
                    'items_count' => count($postIds)
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Orders placed successfully!',
                'orders' => $createdOrders,
                'total_orders' => count($createdOrders)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}