<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtSettings extends Model 
{    
    protected $table = 'et_settings';
    protected $fillable = [
        'boxoffice_id',
        'event_id',
        'option_key',
        'option_value'
    ];    
    
}