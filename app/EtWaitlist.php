<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtWaitlist extends Model 
{    
    protected $table = 'et_waitlist';
    protected $fillable = [
        'unique_code',
        'boxoffice_id',
        'admin_id',
        'event_id',
        'name',
        'email',
        'phone',
        'status'
    ];    
    
}