<?php

namespace App\Model;

use App\Model;
use App\Validator\OrderCheckpoint as Validator;

class OrderCheckpoint extends Model
{
    use Validator;
    protected $primaryKey = "order_chkpt_id";
    protected $table = "order_checkpoint";
    protected $fillable = ['erp_id','order_id','chkpt_id','checkin_datetime','note','status','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
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

    function checkpoint() {
        return $this->belongsTo('App\Model\Checkpoint','chkpt_id', 'chkpt_id')->select(['chkpt_id','erp_id','name','name_ar']);
	}

    function order() {
        return $this->belongsTo('App\Model\Order','order_id', 'order_id');
	}

    function add($data) {
        
        try {
			$ordercheckpoint =  parent::add($data);
			return $ordercheckpoint;
		}
		catch(\Exception $ex){
			Error::trigger("ordercheckpoint.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $order_chkpt_id, array $change_conditions = []) {
        try {
			$ordercheckpoint = parent::change($data, $order_chkpt_id, $change_conditions);
			return $ordercheckpoint;
		} catch(Exception $ex) {
			Error::trigger("ordercheckpoint.change", [$ex->getMessage()]);
		}
    }
}
