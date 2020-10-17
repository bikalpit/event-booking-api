<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtPayment extends Model 
{    
    protected $table = 'et_payment';
    protected $fillable = [
        'order_id',
        'payment_status',
        'amount',
        'payment_method',
        'sub_total',
        'transaction_id',
        'event_id',
        'boxoffice_id',
        'customer_id'
    ];    
    
}