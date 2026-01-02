<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasApiTokens, HasRoles, softDeletes;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'SKU',
        'is_active',
        'image',
        'created_at',
        'updated_at',
    ];
    public function categorys()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }
    public function inStock()
    {
        return $this->stock > 0;
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::addGlobalScope('active', function ($builder) {
            $builder->where('is_active', true);
        });
    }
    public function scopeFilter($query, $filters)
    {
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }
        return $query;
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
