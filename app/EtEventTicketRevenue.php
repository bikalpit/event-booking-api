<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtEventTicketRevenue extends Model 
{    
    protected $table = 'et_event_ticket_revenue';
    protected $fillable = [
        'event_id',
        'ticket_id',
        'sold',
        'remaining',
        'ticket_amt',
        'revenue'
    ];    
    
    public function event()
    {
        return $this->hasOne('App\EtEvent','unique_code','event_id');
    }
}