<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['inquiry_id', 'customer_id', 'status'];

    public function inquiry()
    {
        return $this->belongsTo(Inquiries::class);
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function getProductsAttribute()
    {
        // Ensure inquiry exists and fetch products
        return optional($this->inquiry)->products ?? collect();
    }
}
