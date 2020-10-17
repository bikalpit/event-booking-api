<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtBoxOffice extends Model 
{    
    protected $table = 'et_box_office';
    protected $fillable = [
        'unique_code',
        'box_office_name',
        'admin_id',
        'language',
        'timezone',
        'box_office_link',
        'email_order_notification',
        'account_owner',
        'add_email',
        'hide_tailor_logo',
		'image'
    ];    
	
	protected $appends = ['image_name'];
	public function getImageAttribute($value)
	{
		return env('APP_URL').'boxoffice-images/'.$value;
	}
	
	public function getImageNameAttribute()
	{
		return basename($this->image);
	}
    
}