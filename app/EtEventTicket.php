<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtEventTicket extends Model 
{    
    protected $table = 'et_event_ticket';
    protected $fillable = [
        'event_id',
        'ticket_id'
    ];    
    
}