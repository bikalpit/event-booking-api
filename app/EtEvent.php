<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtEvent extends Model 
{    
    protected $table = 'et_event';
    protected $fillable = [
        'unique_code',
        'admin_id',
        'event_title',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue_name',
        'postal_code',
        'country',
        'online_event',
        'description',
        'platform',
        'event_link',
        'event_status'
    ];    
    
    public function tickets()
    {
      return $this->belongsToMany('App\EtTickets', 'et_event_ticket', 'event_id', 'ticket_id');
    }
}