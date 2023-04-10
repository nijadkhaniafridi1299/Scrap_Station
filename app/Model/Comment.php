<?php

namespace App\Model;

use App\Model;
use App\Validator\Comment as Validator;
use App\Message\Error;

class Comment extends Model
{
    use Validator;
    protected $primaryKey = "comment_id";
    protected $table = "comment";
    protected $fillable = ['erp_id', 'ref_type', 'ref_id', 'comment_text', 'comment_type', 'status', 'created_source', 'updated_source'];
    protected $attributes = ['status' => 1];
    protected static $columns = [
        "created_at" => "Created Date",
        "created_by" => "Created By",
        "updated_at" => "Modified Date",
        "updated_by" => "Modified By",
        "deleted_at" => "Deleted Date"
    ];
    public $timestamps = false;

    function order() {
		return $this->belongsTo('App\Model\Order', 'ref_id', 'order_id')->where("ref_type","order");
	}

    function sellerlistingapplicant() {
		return $this->belongsTo('App\Model\SellerListingApplicant', 'ref_id', 'sell_list_app_id')->where("ref_type","seller_listing_applicant");
	}

    function buyerlistingapplicant() {
		return $this->belongsTo('App\Model\BuyerListingApplicant', 'ref_id', 'buyer_list_app_id')->where("ref_type","buyer_listing_applicant");
	}

    function images() {
        return $this->hasMany('App\Model\Image','ref_id', 'comment_id')->where('ref_type','comment')->select(['img_id', 'ref_id', "path"]);
	}

    function add($data) {
        if(!$this->validate($data)){
			Error::trigger( 'comment.add', $this->getErrors());
			return false;
		}
        try {
			$comment =  parent::add($data);
			return $comment;
		}
		catch(\Exception $ex){
			Error::trigger("comment.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $comment_id, array $change_conditions = []) {

    }
}
