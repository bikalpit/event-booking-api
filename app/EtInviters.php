<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtInviters extends Model 
{    
    protected $table = 'et_inviters';
    protected $fillable = [
        'unique_code',
        'admin_id',
        'email_id',
        'status',
        'invite_datetime',
        'verify_token',
        'role',
        'permission',
        'sub_permission'
    ];    
    
}