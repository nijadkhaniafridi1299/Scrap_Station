<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;
use App\Validator\SellerListingApplicant as Validator;

class SellerListingApplicant extends Model
{
    use Validator;
    protected $primaryKey = "sell_list_app_id";
    protected $table = "seller_listing_applicant";
    protected $fillable = ['erp_id','sell_list_id','buyer_id','sell_list_app_no','note','status','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
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
    protected $appends = ['buyername','sellername',"chatsource"];

    function getBuyernameAttribute() {
        $buyer_name = $this->buyer->fullname;
        unset($this->buyer);
        return $buyer_name;
	}

    function getSellernameAttribute() {
        $seller_name = $this->sellerlist->seller->fullname;
        unset($this->sellerlist);
		return $seller_name;
	}

    function getChatsourceAttribute() {
        return "seller_listing_applicant";
	}

    function offers() {
        return $this->hasMany('App\Model\Offer', 'application_id','sell_list_app_id')->where("application_source","seller_listing_applicant")->select(['offer_id','erp_id','offer_no','application_source','application_id','type','previous_offer_id','expected_arrival_date','offered_price','address_id','status','created_at','updated_at']);
	}

    function buyer() {
        return $this->belongsTo('App\Model\Buyer','buyer_id', 'buyer_id')->select(['buyer_id','fullname','fullname_ar','email','mobile']);
	}

    function comments() {
        return $this->hasMany('App\Model\Comment', 'ref_id','sell_list_app_id')->where("ref_type","seller_listing_applicant");
	}

    function sellerlist() {
        return $this->belongsTo('App\Model\SellerListing','sell_list_id', 'sell_list_id');
	}

    function add($data) {
        
        try {
			$sellerlistingapplicant =  parent::add($data);
			return $sellerlistingapplicant;
		}
		catch(\Exception $ex){
			Error::trigger("sellerlistingapplicant.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $sell_list_app_id, array $change_conditions = []) {
        try {
			$sellerlistingapplicant = parent::change($data, $sell_list_app_id, $change_conditions);
			return $sellerlistingapplicant;
		} catch(Exception $ex) {
			Error::trigger("sellerlistingapplicant.change", [$ex->getMessage()]);
		}
    }
}
