<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtCurreincies extends Model 
{    
    protected $table = 'et_curreincies';
    protected $fillable = [
        'CurrencyCode',
        'CurrencyName'
    ];    
}