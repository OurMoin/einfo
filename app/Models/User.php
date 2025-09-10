<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'job_title',
        'username',
        'otp',
        'country_id',
        'city_id',
        'area',
        'category_id',
        // অন্যান্য fields...
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Category relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Posts relationship
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Country relationship
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // City relationship
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the display name for job title or business category
     */
    public function getJobDisplayAttribute()
    {
        if ($this->category_id && $this->category) {
            return $this->category->category_name;
        }
        
        return $this->job_title;
    }

    /**
     * Check if user has a predefined category or custom job title
     */
    public function hasPredefinedCategory()
    {
        return !is_null($this->category_id) && !is_null($this->category);
    }

    /**
     * Get the job title for form display (current value)
     */
    public function getJobTitleForFormAttribute()
    {
        if ($this->category_id && $this->category) {
            return $this->category->category_name;
        }
        
        return $this->job_title;
    }

    /**
     * Get full location string
     */
    public function getFullLocationAttribute()
    {
        $location = [];
        
        if ($this->area) {
            $location[] = $this->area;
        }
        
        if ($this->city) {
            $location[] = $this->city->name;
        }
        
        if ($this->country) {
            $location[] = $this->country->name;
        }
        
        return implode(', ', $location);
    }
}