<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'total_amount',
        'status',
        'shipping_address',
        'phone',
        'post_ids'
    ];

    protected $casts = [
        'post_ids' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
    
    public function getOrderedPostsWithDetails()
    {
        if (!$this->post_ids) return collect([]);
        
        $postIds = collect($this->post_ids)->pluck('post_id')->toArray();
        $posts = \App\Models\Post::whereIn('id', $postIds)->get();
        
        return $posts->map(function ($post) {
            $orderItem = collect($this->post_ids)->firstWhere('post_id', $post->id);
            $post->ordered_quantity = $orderItem['quantity'] ?? 0;
            $post->service_time = $orderItem['service_time'] ?? null;
            return $post;
        });
    }
}