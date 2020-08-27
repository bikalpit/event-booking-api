<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtOrders extends Model 
{    
    protected $table = 'et_orders';
    protected $fillable = [
        'unique_code',
        'event_id',
        'customer_id',
        'order_date',
        'order_time',
        'amount',
        'status'
    ];    
    
}