<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;
use App\Message\Error;

class Image extends Model
{
	protected $primaryKey = 'img_id';
	protected $table = 'images';
	protected $fillable = ['erp_id','ref_type','ref_id','path','note','status','created_source','created_at','created_by','updated_source','updated_at','updated_by'];
    protected $hidden = ['created_source','created_at','created_by','updated_source','updated_at','updated_by','deleted_at'];
	public $timestamps = true;

    function add($data) {
        try {
			$image =  parent::add($data);
			return $image;
		}
		catch(\Exception $ex){
			Error::trigger("images.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $img_id, array $change_conditions = []) {
        try {
			$image = parent::change($data, $img_id, $change_conditions);
			return $image;
		} catch(Exception $ex) {
			Error::trigger("images.change", [$ex->getMessage()]);
		}
    }

    function typeCategories() {
		return $this->hasMany('App\Model\Category', 'ref_id', 'cat_id');
	}
    function typeSubCategories() {
		return $this->hasMany('App\Model\SubCategory', 'ref_id', 'sub_cat_id');
	}
    function typeSellerProfiles() {
		return $this->hasMany('App\Model\Seller', 'ref_id', 'seller_id');
	}
    function typeSellerIqamas() {
		return $this->hasMany('App\Model\Seller', 'ref_id', 'seller_id');
	}
    function typeBuyerProfiles() {
		return $this->hasMany('App\Model\Buyer', 'ref_id', 'buyer_id');
	}
    function typeBuyerIqamas() {
		return $this->hasMany('App\Model\Buyer', 'ref_id', 'buyer_id');
	}
    function typeOrders() {
		return $this->hasMany('App\Model\Order', 'ref_id', 'order_id');
	}
    function typeBuyerListApplicants() {
		return $this->hasMany('App\Model\BuyerListApplicant', 'ref_id', 'buyer_list_app_id');
	}
    function typePayments() {
		return $this->hasMany('App\Model\Payment', 'ref_id', 'pay_id');
	}
    function typeOffers() {
		return $this->hasMany('App\Model\Offer', 'ref_id', 'offer_id');
	}
    function typeMaterials() {
		return $this->hasMany('App\Model\Material', 'ref_id', 'mat_id');
	}
    function typeSellerListings() {
		return $this->hasMany('App\Model\SellerListing', 'ref_id', 'sell_list_id');
	}
    function typeCheckpoints() {
		return $this->hasMany('App\Model\Checkpoint', 'ref_id', 'chkpt_id');
	}
    function typeComments() {
		return $this->hasMany('App\Model\Comment', 'ref_id', 'comment_id');
	}

    function imagetype(){
        if($this->ref_type == "category"){ return $this->typeCategories(); }
        else if($this->ref_type == "subcategory"){ return $this->typeSubCategories(); }
        else if($this->ref_type == "seller_profile"){ return $this->typeSellerProfiles(); }
        else if($this->ref_type == "seller_iqama"){ return $this->typeSellerIqamas(); }
        else if($this->ref_type == "buyer_profile"){ return $this->typeBuyerProfiles(); }
        else if($this->ref_type == "buyer_iqama"){ return $this->typeBuyerIqamas(); }
        else if($this->ref_type == "order"){ return $this->typeOrders(); }
        else if($this->ref_type == "buyer_list_applicant"){ return $this->typeBuyerListApplicants(); }
        else if($this->ref_type == "payment"){ return $this->typePayments(); }
        else if($this->ref_type == "offer"){ return $this->typeOffers(); }
        else if($this->ref_type == "material"){ return $this->typeMaterials(); }
        else if($this->ref_type == "seller_listing"){ return $this->typeSellerListings(); }
		else if($this->ref_type == "checkpoint"){ return $this->typeCheckpoints(); }
		else if($this->ref_type == "comment"){ return $this->typeComments(); }
    }
}
