<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category',
        'sizes',
        'colors',
        'images',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'sizes' => 'array',
            'colors' => 'array',
            'price' => 'decimal:2',
        ];
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getMainImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return Storage::url($this->images[0]);
        }
        return Storage::url('products/default-image.jpeg');
    }

    public static function getCategories()
    {
        return ['Jeans', 'Jackets', 'Shirts', 'Shorts', 'Vests'];
    }

    public static function getSizes()
    {
        return ['S', 'M', 'L', 'XL'];
    }

    public static function getColors()
    {
        return ['Blue', 'Black', 'White'];
    }
}