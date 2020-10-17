<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtSalesTax extends Model 
{    
    protected $table = 'et_sales_tax';
    protected $fillable = [
        'unique_code',
        'boxoffice_id',
        'name',
        'value',
        'status'
    ];    
    
}