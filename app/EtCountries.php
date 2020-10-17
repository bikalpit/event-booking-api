<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtCountries extends Model 
{    
    protected $table = 'et_countries';
    protected $fillable = [
        'sortname',
        'name'
    ];    
}