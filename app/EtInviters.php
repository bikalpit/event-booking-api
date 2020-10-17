<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtInviters extends Model 
{    
    protected $table = 'et_inviters';
    protected $fillable = [
        'unique_code',
        'admin_id',
        'name',
        'email_id',
        'status',
        'image',
        'invite_datetime',
        'verify_token',
        'role',
        'permission',
        'sub_permission'
    ];
    
    public function getImageAttribute($value)
    {
        return env('APP_URL').'user-images/'.$value;
    }    
}