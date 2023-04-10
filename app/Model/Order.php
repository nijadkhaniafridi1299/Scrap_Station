<?php

namespace App\Model;

use App\Model;
use App\Validator\Order as Validator;


class Order extends Model
{
    use Validator;
    protected $primaryKey = "order_id";
    protected $table = "order";
    protected $fillable = ['erp_id','order_no','offer_id','arrival_date','mat_id','driver_id','quantity','quantity_unit','price','total_price','address_id','status','is_verified','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
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
        return $this->hasMany('App\Model\Image','ref_id', 'order_id')->where('ref_type','order')->select(['img_id', 'ref_id', "path"]);
	}

    function offer() {
        return $this->belongsTo('App\Model\Offer','offer_id', 'offer_id');
	}

    function material() {
        return $this->belongsTo('App\Model\Material','mat_id', 'mat_id');
	}

    function driver() {
        return $this->belongsTo('App\Model\Driver','driver_id', 'driver_id')->select(['driver_id','fullname','fullname_ar','email','mobile']);
	}

    function payment() {
        return $this->hasOne('App\Model\Payment','ref_id', 'order_id')->where('ref_type','order');
	}

    function address() {
        return $this->belongsTo('App\Model\Address','address_id', 'address_id')->select(['address_id','erp_id','address','longitude','latitude','address_title']);
	}
    function systemstatus() {
        return $this->belongsTo('App\Model\SystemStatus','status', 'id')->select(['id','key','name','name_ar','value','is_active']);
	}

    function checkpoints() {
        return $this->hasMany('App\Model\OrderCheckpoint','order_id', 'order_id')->select(['order_chkpt_id','erp_id','order_id','chkpt_id','checkin_datetime','note']);
	}

    function review() {
        return $this->hasOne('App\Model\OrderReview','order_id', 'order_id');
	}

    function add($data) {
        $order_no = generateNumber("Order", "created_by", $data['created_by']);
        $data['order_no'] = $order_no;
        try {
			$order =  parent::add($data);
			return $order;
		}
		catch(\Exception $ex){
			Error::trigger("order.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $order_id, array $change_conditions = []) {
        try {
			$order = parent::change($data, $order_id, $change_conditions);
			return $order;
		} catch(Exception $ex) {
			Error::trigger("order.change", [$ex->getMessage()]);
		}
    }
}
