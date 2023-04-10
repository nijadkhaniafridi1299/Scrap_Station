<?php

namespace App\Model;

use App\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Message\Error;
use App\Validator\Complaint as Validator;
use DB;

class Complaint extends Model
{
    use Validator;

    protected $primaryKey = "complaint_id";
    protected $table = "complaint";
    protected $fillable = [
      'erp_id', 'seller_id', 'complaint_no', 'parent_id',
      'item_source', 'item_id', 'complaint_type', 'complaint_text',
      'assigned_to_source', 'assigned_to_id', 'assigned_by_source', 'assigned_by_id',
      'complaint_priority', 'complaint_img', 'reason',
      'status', 'created_source', 'created_by', 'updated_source', 'updated_by', 'updated_at'
    ];
    
    function assignedToSeller() {
      return $this->belongsTo('App\Model\Seller', 'assigned_to_id', 'seller_id')->select(['seller_id as assign_id', 'email',"fullname AS username"]);
    }
  
      function assignedToBuyer() {
      return $this->belongsTo('App\Model\Buyer', 'assigned_to_id', 'buyer_id')->select(['buyer_id as assign_id', 'email',"fullname AS username"]);;
    }
  
      function assignedToUser() {
      return $this->belongsTo('App\Model\User', 'assigned_to_id', 'user_id')->select(['user_id as assign_id', 'email',DB::raw("CONCAT(first_name,' ',last_name) AS username")]);
    }
  
      function assignedToDriver() {
      return $this->belongsTo('App\Model\Driver', 'assigned_to_id', 'driver_id')->select(['driver_id as assign_id', 'email',DB::raw("CONCAT(first_name,' ',last_name) AS username")]);
    }
  
    function assignedToPerson(){
          if(empty($this->assigned_to_source)){ return null; }
          if($this->assigned_to_source == "seller"){ return $this->assignedToSeller(); }
          else if($this->assigned_to_source == "buyer"){ return $this->assignedToBuyer(); }
          else if($this->assigned_to_source == "user"){ return $this->assignedToUser(); }
          else if($this->assigned_to_source == "driver"){ return $this->assignedToDriver(); }
      }

      function assignedBySeller() {
        return $this->belongsTo('App\Model\Seller', 'assigned_by_id', 'seller_id')->select(['seller_id as assign_id', 'email',"fullname AS username"]);
      }
    
        function assignedByBuyer() {
        return $this->belongsTo('App\Model\Buyer', 'assigned_by_id', 'buyer_id')->select(['buyer_id as assign_id', 'email',"fullname AS username"]);
      }
    
        function assignedByUser() {
        return $this->belongsTo('App\Model\User', 'assigned_by_id', 'user_id')->select(['user_id as assign_id', 'email',DB::raw("CONCAT(first_name,' ',last_name) AS username")]);
      }
    
        function assignedByDriver() {
        return $this->belongsTo('App\Model\Driver', 'assigned_by_id', 'driver_id')->select(['driver_id as assign_id', 'email',DB::raw("CONCAT(first_name,' ',last_name) AS username")]);
      }
    
      function assignedByPerson(){
            if(empty($this->assigned_by_source)){ return null; }
            if($this->assigned_by_source == "seller"){ return $this->assignedBySeller(); }
            else if($this->assigned_by_source == "buyer"){ return $this->assignedByBuyer(); }
            else if($this->assigned_by_source == "user"){ return $this->assignedByUser(); }
            else if($this->assigned_by_source == "driver"){ return $this->assignedByDriver(); }
      }
    
      function order() {
        return $this->belongsTo('App\Model\Order', 'item_id', 'order_id')->select(['order_id as ref_id', 'order_no as ref_no']);
      } 
      function payment() {
        return $this->belongsTo('App\Model\Payment', 'item_id', 'pay_id')->select(['pay_id as ref_id', 'pay_no as ref_no']);
      }
      function complaintAgainstOP(){
        if(empty($this->item_source)){ return null; }
        if($this->item_source == "order"){ return $this->order(); }
        else if($this->item_source == "payment"){ return $this->payment(); }
      } 
      function history() {
          return $this->hasMany('App\Model\ComplaintHistory', 'complaint_id','complaint_id');
      }
      function comments() {
        return $this->hasMany('App\Model\Comment', 'ref_id','complaint_id')->where("ref_type","complaint");
	    }
      function seller() {
        return $this->belongsTo('App\Model\Seller','seller_id', 'seller_id')->select(['seller_id','fullname','fullname_ar','email','mobile']);
      }
      function systemstatus() {
        return $this->belongsTo('App\Model\SystemStatus','status', 'id')->select(['id','key','name','name_ar','value','is_active']);
    	}
 

      function add($data) {
        if(!$this->validate($data)){
          Error::trigger( 'complaint.add', $this->getErrors());
          return false;
        }
        try {
          $complaint =  parent::add($data);
          return $complaint;//->toArray();
        }
        catch(\Exception $ex){
          Error::trigger("complaint.add", [$ex->getMessage()]);
          return [];
        }
      }
  
      function change(array $data, $complaint_id, array $change_conditions = []) {
        $complaint = static::find($complaint_id);
        try {
          $complaint = parent::change($data, $complaint_id, $change_conditions);
          return $complaint;
        } catch(Exception $ex) {
          Error::trigger("complaint.change", [$ex->getMessage()]);
        }
      }
}
