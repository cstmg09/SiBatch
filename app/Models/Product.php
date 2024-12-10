<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'description',
        'price',
        'stock',
        'is_available'
    ];

    public function inquiries()
    {
        return $this->belongsToMany(Inquiries::class, 'inquiry_product', 'product_id', 'inquiry_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
