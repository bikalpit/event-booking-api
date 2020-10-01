<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtTickets extends Model 
{    
    protected $table = 'et_tickets';
    protected $fillable = [
        'unique_code',
        'box_office_id',
        'event_id',
        'ticket_name',
        'prize',
        'qty',
        'advance_setting',
        'description',
        'booking_fee',
        'status',
        'min_per_order',
        'max_per_order',
        'hide_untill',
        'hide_after',
        'untill_date',
        'untill_time',
        'after_date',
        'after_time',
        'sold_out',
        'show_qty',
        'discount'
    ];    
	
	public function orders(){
        return $this->hasOne('App\EtOrders', 'unique_code','ticket_id');
    }
    
}