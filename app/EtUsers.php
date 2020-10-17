<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtUsers extends Model 
{    
    protected $table = 'et_users';
    protected $fillable = [
        'unique_code',
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'role',
        'permission',
        'sub_permission',
        'status',
        'email_verify',
        'image'
    ];    
    
    protected $hidden = [ 'password'];

    public function getImageAttribute($value)
    {
        return env('APP_URL').'user-images/'.$value;
    }
}