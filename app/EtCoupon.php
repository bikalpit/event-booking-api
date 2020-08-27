<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtCoupon extends Model 
{    
    protected $table = 'et_coupon';
    protected $fillable = [
        'unique_code',
        'coupon_title',
        'coupon_code',
        'valid_from',
        'max_redemption',
        'discount_type',
        'discount',
        'valid_till'
    ];    
    
}