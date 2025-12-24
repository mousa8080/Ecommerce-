<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Product extends Model
{
    use HasFactory,HasApiTokens,HasRoles;
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
}
