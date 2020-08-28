<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
class Api_auth extends Model implements Authenticatable
{
   use AuthenticableTrait;
   protected $table = 'et_api_token';

   protected $fillable = ['user_id','token','user_type'];

   protected $hidden = [
   ];
}