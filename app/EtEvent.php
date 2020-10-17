<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtEvent extends Model
{
    protected $table = 'et_event';
    protected $fillable = [
        'unique_code',
        'admin_id',
        'event_title',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue_name',
        'postal_code',
        'country',
        'online_event',
        'description',
        'platform',
        'event_link',
        'event_status'
    ];
    
    public function tickets()
    {
      return $this->belongsToMany('App\EtTickets', 'et_event_ticket', 'event_id', 'ticket_id');
    }

    public function eventTickets()
    {
      return $this->hasMany('App\EtEventTicket', 'event_id', 'unique_code');
    }

    public function revenue()
    {
      return $this->hasMany('App\EtEventTicketRevenue', 'event_id', 'unique_code');
    }

    public function soldout()
    {
      return $this->revenue()
            ->selectRaw('sum(sold) as Sold, event_id')
            ->groupBy('event_id');
    }

    public function remaining()
    {
      return $this->revenue()
            ->selectRaw('sum(remaining) as Remaining, event_id')
            ->groupBy('event_id');
    }

    public function finalRevenue()
    {
      return $this->revenue()
            ->selectRaw('sum(revenue) as Revenue, event_id')
            ->groupBy('event_id');
    }

    public function eventSetting()
    {
        return $this->hasOne('App\EtEventSetting', 'event_id', 'unique_code');
    }

    public function images()
    {
      return $this->hasMany('App\EtEventImage','event_id','unique_code');
    }
    public function country()
    {
      return $this->hasMany('App\EtCountries','id','country');
    }
	public function getDescriptionAttribute($value)
	{
		return strip_tags($value,"<p>\n");
	}
}