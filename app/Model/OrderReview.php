<?php

namespace App\Model;

use App\Model;
use App\Validator\OrderReview as Validator;

class OrderReview extends Model
{
    use Validator;
    protected $primaryKey = "review_id";
    protected $table = "order_review";
    protected $fillable = ['erp_id','order_id','stars','note','status','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
    protected $hidden = ['created_source','created_by','updated_source','updated_by','deleted_at'];
    protected static $columns = [
        "created_at" => "Created Date",
        "created_by" => "Created By",
        "updated_at" => "Modified Date",
        "updated_by" => "Modified By",
        "deleted_at" => "Deleted Date"
    ];
    public $timestamps = true;

    function images() {
        return $this->hasMany('App\Model\Image','ref_id', 'review_id')->where('ref_type','review')->select(['img_id', 'ref_id', "path"]);
	}

    function order() {
        return $this->belongsTo('App\Model\Order','order_id', 'order_id');
	}

    function add($data) {
        try {
			$review =  parent::add($data);
			return $review;
		}
		catch(\Exception $ex){
			Error::trigger("review.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $review_id, array $change_conditions = []) {
        try {
			$review = parent::change($data, $review_id, $change_conditions);
			return $review;
		} catch(Exception $ex) {
			Error::trigger("review.change", [$ex->getMessage()]);
		}
    }
}
