<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtEventImage extends Model 
{    
    protected $table = 'et_event_image';
    protected $fillable = [
        'unique_code',
        'event_id',
        'image',
        'type'
    ];
    protected $appends = ['image_name'];
    public function getImageAttribute($value)
    {
        return env('APP_URL').'event-images/'.$value;
    }
    public function getImageNameAttribute()
    {
        return $this->attributes['image'];
    }
}