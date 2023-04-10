<?php

namespace App\Model;
use App\Model;
use App\Validator\Payment as Validator;
use App\Message\Error;

class Payment extends Model 
{

	use Validator;
	protected $table = "payment";
	protected $primaryKey = "pay_id";
    protected $fillable = ["erp_id","pay_no","ref_type","ref_id","pay_amount","paid_amount","received_by","status",'is_verified',"created_source","created_by","created_at","updated_source","updated_by","updated_at"];
	protected $hidden = ['created_source','created_at','created_by','updated_source','updated_at','updated_by','deleted_at'];
    public $timestamps = false;

	function order() {
        return $this->belongsTo('App\Model\Order','ref_id', 'order_id');//->where("ref_type","order");
	}

	function systemstatus() {
        return $this->belongsTo('App\Model\SystemStatus','status', 'id')->select(['id','key','name','name_ar','value','is_active']);
	}

	function add($data) {
        try {
			$payment =  parent::add($data);
			return $payment;
		}
		catch(\Exception $ex){
			Error::trigger("payment.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $spr_mat_id, array $change_conditions = []) {
        try {
			$payment = parent::change($data, $spr_mat_id, $change_conditions);
			return $payment;
		} catch(Exception $ex) {
			Error::trigger("payment.change", [$ex->getMessage()]);
		}
    }

}
