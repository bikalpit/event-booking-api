<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class EtEventSetting extends Model 
{    
    protected $table = 'et_event_setting';
    protected $fillable = [
        'unique_code',
        'event_id',
        'timezone',
        'make_donation',
        'event_button_title',
        'donation_title',
        'donation_amt',
        'donation_description',
        'ticket_avilable',
        'ticket_unavilable',
        'redirect_confirm_page',
        'redirect_url',
        'hide_office_listing',
        'customer_access_code',
        'access_code',
        'hide_share_button',
        'custom_sales_tax',
        'sales_tax_amt',
        'sales_tax_label',
        'sales_tax_id'
        
    ];    
    
}