<?php

namespace App\Model;

use App\Model;
use App\Validator\BuyerListingApplicant as Validator;

class BuyerListingApplicant extends Model
{
    use Validator;
    protected $primaryKey = "buyer_list_app_id";
    protected $table = "buyer_listing_applicant";
    protected $fillable = ['erp_id','buyer_list_id','seller_id','buy_list_app_no','note','status','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
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
    protected $appends = ['sellername','buyername','chatsource'];

    function getBuyernameAttribute() {
        $buyer_name = $this->buyerlist->buyer->fullname;
        unset($this->buyerlist);
        return $buyer_name;
	}

    function getChatsourceAttribute() {
        return "buyer_listing_applicant";
	}

    function getSellernameAttribute() {
        $seller_name = $this->seller->fullname;
        unset($this->seller);
        return $seller_name;
	}

    function offers() {
        return $this->hasMany('App\Model\Offer', 'application_id','buyer_list_app_id')->where("application_source","buyer_listing_applicant")->select(['offer_id','erp_id','offer_no','application_source','application_id','type','previous_offer_id','expected_arrival_date','offered_price','address_id','status','created_at','updated_at']);
	}

    function seller() {
        return $this->belongsTo('App\Model\Seller','seller_id', 'seller_id')->select(['seller_id','fullname','fullname_ar','email','mobile']);
	}

    function buyerlist() {
        return $this->belongsTo('App\Model\BuyerListing','buyer_list_id', 'buyer_list_id');
	}

    function comments() {
        return $this->hasMany('App\Model\Comment', 'ref_id','buyer_list_app_id')->where("ref_type","buyer_listing_applicant");
	}

    function add($data) {
        
        try {
			$buyerlistingapplicant =  parent::add($data);
			return $buyerlistingapplicant;
		}
		catch(\Exception $ex){
			Error::trigger("buyerlistingapplicant.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $buy_list_app_id, array $change_conditions = []) {
        try {
			$buyerlistingapplicant = parent::change($data, $buy_list_app_id, $change_conditions);
			return $buyerlistingapplicant;
		} catch(Exception $ex) {
			Error::trigger("buyerlistingapplicant.change", [$ex->getMessage()]);
		}
    }
}
