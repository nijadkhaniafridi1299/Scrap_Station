<?php

namespace App\Model;

use App\Model;
use App\Validator\SellerListing as Validator;

class SellerListing extends Model
{
    use Validator;
    protected $primaryKey = "sell_list_id";
    protected $table = "seller_listing";
    protected $fillable = ['erp_id','seller_id','mat_id','listing_no','quantity','quantity_unit','expected_price_per_unit','expected_price','type','no_of_days','promo_code','address_id','status','is_verified','closed_reason','active_days','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
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
        return $this->hasMany('App\Model\Image','ref_id', 'sell_list_id')->where('ref_type','seller_listing')->select(['img_id', 'ref_id', "path"]);
	}

    function address() {
        return $this->belongsTo('App\Model\Address','address_id', 'address_id')->select(['address_id','erp_id','address','longitude','latitude','address_title']);
	}

    function material() {
        return $this->belongsTo('App\Model\Material','mat_id', 'mat_id')->select(['mat_id','sub_cat_id','erp_id','product_code','name','name_ar']);
	}

    function applicants() {
        return $this->hasMany('App\Model\SellerListingApplicant','sell_list_id', 'sell_list_id');
	}

    function offers() {
        return $this->hasMany('App\Model\Offer','listing_id', 'sell_list_id')->where("listing_source","seller_listing");
	}
    function seller() {
        return $this->belongsTo('App\Model\Seller','seller_id', 'seller_id')->select(['seller_id','fullname','fullname_ar','email','mobile']);
    } 
    function systemstatus() {
        return $this->belongsTo('App\Model\SystemStatus','status', 'id')->select(['id','key','name','name_ar','value','is_active']);
	}

    function openoffers() {
        return $this->offers()->whereIn("status",[2,6]);
	}

    function add($data) {
        $data['type'] = "individual";
        if(!isset($data['quantity_unit']) || empty($data['quantity_unit']) || $data['quantity_unit']==null){ $data['quantity_unit'] = "kg"; }
        if(isset($data['no_of_days']) && $data['no_of_days']>1){ $data['type'] = "bulk"; }

        if(!isset($data['expected_price'])){ $data['expected_price'] = $data['expected_price_per_unit'] * $data['quantity']; }
        
        try {
			$sellerlisting =  parent::add($data);
			return $sellerlisting;
		}
		catch(\Exception $ex){
			Error::trigger("sellerlisting.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $sell_list_id, array $change_conditions = []) {
        try {
			$sellerlisting = parent::change($data, $sell_list_id, $change_conditions);
			return $sellerlisting;
		} catch(Exception $ex) {
			Error::trigger("sellerlisting.change", [$ex->getMessage()]);
		}
    }
}
