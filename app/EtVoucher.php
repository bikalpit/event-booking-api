<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtVoucher extends Model 
{    
    protected $table = 'et_voucher';
    protected $fillable = [
        'unique_code',
        'boxoffice_id',
        'voucher_name',
        'voucher_value',
        'voucher_code',
        'expiry_date',
        'event_id'
    ];    
    
}