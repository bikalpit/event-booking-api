<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtEventImage extends Model 
{    
    protected $table = 'et_event_image';
    protected $fillable = [
        'unique_code',
        'event_id',
        'image'
    ];    
    
}