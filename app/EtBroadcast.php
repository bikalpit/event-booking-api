<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtBroadcast extends Model 
{    
    protected $table = 'et_broadcast';
    protected $fillable = [
        'unique_code',
        'event_id',
        'recipients',
        'subject',
        'message',
        'send',
        'terms'
    ];    
    
}