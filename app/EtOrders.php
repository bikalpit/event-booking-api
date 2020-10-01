<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtOrders extends Model 
{    
     protected $table = 'et_orders';
    protected $fillable = [
        'unique_code',
        'boxoffice_id',
        'event_id',
        'qty',
        'ticket_id',
        'sub_total',
        'discount_code',
        'discount_amt',
        'voucher_code',
        'voucher_amt',
        'customer_id',
        'order_date',
        'order_time',
        'grand_total',
        'order_status'
    ];  

    public function customer(){  
        return $this->belongsTo('App\EtCustomers','customer_id','unique_code');    
    } 

    public function ticket(){  
        return $this->belongsTo('App\EtTickets','ticket_id','unique_code');    
    }    
    
}