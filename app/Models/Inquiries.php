<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiries extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'address',
        'message',
        'total',
        'inquiries_status',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'inquiry_product', 'inquiry_id', 'product_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

}
