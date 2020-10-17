<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtTicketCoupon extends Model 
{    
    protected $table = 'et_ticket_coupon';
    protected $fillable = [
        'ticket_id',
        'coupon_id'
    ];       
}