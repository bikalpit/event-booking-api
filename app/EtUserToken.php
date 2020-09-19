<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtUserToken extends Model 
{    
    protected $table = 'et_user_token';
    protected $fillable = [
        'email',
        'token',
        'type'
    ];    
}