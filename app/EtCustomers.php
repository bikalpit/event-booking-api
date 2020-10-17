<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtCustomers extends Model 
{    
    protected $table = 'et_customers';
    protected $fillable = [
        'unique_code',
		'boxoffice_id',
        'email',
        'phone',
        'firstname',
        'lastname',
        'address',
        'email_verify',
        'image',
        'tags',
        'customer_data'
    ];    

    protected $appends = ['name'];

    public function getImageAttribute($value)
    {
        return env('APP_URL').'customer-images/'.$value;
    }
    public function orders(){
        return $this->hasMany('App\EtOrders', 'customer_id', 'unique_code');
    }
    public function getNameAttribute()
    {
        return $this->firstname.' '.$this->lastname;
    }
   
}