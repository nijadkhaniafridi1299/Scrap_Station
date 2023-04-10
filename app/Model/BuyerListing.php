<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;
use App\Validator\BuyerListing as Validator;

class BuyerListing extends Model
{
    use Validator;
    protected $primaryKey = "buyer_list_id";
    protected $table = "buyer_listing";
    protected $fillable = ['erp_id','buyer_id','mat_id','listing_no','quantity','quantity_unit','expected_price_per_unit','expected_price','promo_code','address_id','status','is_verified','closed_reason','active_days','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
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

    function images() {
        return $this->hasMany('App\Model\Image','ref_id', 'buyer_list_id')->where('ref_type','buyer_listing')->select(['img_id', 'ref_id', "path"]);
	}

    function address() {
        return $this->belongsTo('App\Model\Address','address_id', 'address_id')->select(['address_id','erp_id','address','longitude','latitude','address_title']);
	}

    function buyer() {
        return $this->belongsTo('App\Model\Buyer','buyer_id', 'buyer_id')->select(['buyer_id','erp_id','fullname','email','mobile']);
	}

    function material() {
        return $this->belongsTo('App\Model\Material','mat_id', 'mat_id')->select(['mat_id','sub_cat_id','erp_id','product_code','name','name_ar']);
	}

    function applicants() {
        return $this->hasMany('App\Model\BuyerListingApplicant','buyer_list_id', 'buyer_list_id');
	}

    function offers() {
        return $this->hasMany('App\Model\Offer','listing_id', 'buyer_list_id')->where("listing_source","buyer_listing");
	}

    function openoffers() {
        return $this->offers()->whereIn("status",[2,6]);
	}

    function systemstatus() {
        return $this->belongsTo('App\Model\SystemStatus','status', 'id')->select(['id','key','name','name_ar','value','is_active']);
	}

    function add($data) {
        if(!isset($data['quantity_unit']) || empty($data['quantity_unit']) || $data['quantity_unit']==null){ $data['quantity_unit'] = "kg"; }

        if(!isset($data['expected_price'])){ $data['expected_price'] = $data['expected_price_per_unit'] * $data['quantity']; }
        
        try {
			$buyerlisting =  parent::add($data);
			return $buyerlisting;
		}
		catch(\Exception $ex){
			Error::trigger("buyerlisting.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $buyer_list_id, array $change_conditions = []) {
        try {
			$buyerlisting = parent::change($data, $buyer_list_id, $change_conditions);
			return $buyerlisting;
		} catch(Exception $ex) {
			Error::trigger("buyerlisting.change", [$ex->getMessage()]);
		}
    }
}
