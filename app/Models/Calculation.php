<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;
    protected $fillable = [
        'purchase_price',
        'logistics_cost',
        'quantity',
        'tax_rate',
        'selling_price',
        'margin_percentage',
        'category_commission_fbs',
        'category_commission_fbo',
        'height',
        'length',
        'depth',
    ];
}
