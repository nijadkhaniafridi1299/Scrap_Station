<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;
use App\Validator\Offer as Validator;

class Offer extends Model
{
    use Validator;
    protected $primaryKey = "offer_id";
    protected $table = "offer";
    protected $fillable = ['erp_id','offer_no','seller_id','buyer_id','listing_source','listing_id','application_source','application_id','type','previous_offer_id','expected_arrival_date','offered_price','offered_price_with_vat','address_id','reason','status','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
    protected $hidden = ['created_source','created_by','updated_source','updated_by','deleted_at'];
    // protected $attributes = ['is_synced'=>0];
    protected static $columns = [
        "created_at" => "Created Date",
        "created_by" => "Created By",
        "updated_at" => "Modified Date",
        "updated_by" => "Modified By",
        "deleted_at" => "Deleted Date"
    ];
    public $timestamps = true;

    function seller() {
        return $this->belongsTo('App\Model\Seller','seller_id', 'seller_id')->select(['seller_id','fullname','fullname_ar','email','mobile']);
	}

    function buyer() {
        return $this->belongsTo('App\Model\Buyer','buyer_id', 'buyer_id')->select(['buyer_id','fullname','fullname_ar','email','mobile']);
	}

    function sellerlisting() {
        return $this->belongsTo('App\Model\SellerListing','listing_id', 'sell_list_id');
	}

    function buyerlisting() {
        return $this->belongsTo('App\Model\BuyerListing','listing_id', 'buyer_list_id');
	}
    function address() {
        return $this->belongsTo('App\Model\Address','address_id', 'address_id')->select(['address_id','erp_id','address','longitude','latitude','address_title']);
	}
    function systemstatus() {
        return $this->belongsTo('App\Model\SystemStatus','status', 'id')->select(['id','key','name','name_ar','value','is_active']);
	}
    
    function listing(){
        if($this->listing_source == "seller_listing"){ return $this->sellerlisting(); }
        else if($this->listing_source == "buyer_listing"){ return $this->buyerlisting(); }
    }

    function order() {
        return $this->hasOne('App\Model\Order','offer_id', 'offer_id');
	}

    function add($data) {
        
        try {
			$offer =  parent::add($data);
			return $offer;
		}
		catch(\Exception $ex){
			Error::trigger("offer.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $offer_id, array $change_conditions = []) {
        try {
			$offer = parent::change($data, $offer_id, $change_conditions);
			return $offer;
		} catch(Exception $ex) {
			Error::trigger("offer.change", [$ex->getMessage()]);
		}
    }
}
