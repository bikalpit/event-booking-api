<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtSettings extends Model 
{    
    protected $table = 'et_settings';
    protected $fillable = [
        'unique_code',
        'admin_id',
        'option_key',
        'option_value'
    ];    
    
}