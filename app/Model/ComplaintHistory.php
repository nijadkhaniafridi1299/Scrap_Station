<?php

namespace App\Model;

use App\Model;
use App\Message\Error;
use DB;

class ComplaintHistory extends Model
{
    protected $primaryKey = "complaint_history_id";
    protected $table = "complaint_history";
    protected $fillable = [
        'complaint_id', 'assigned_by_source', 'assigned_by_id',
        'assigned_to_source', 'assigned_to_id',
        'status', 'complaint_priority', 'deleted_at'
    ];
    
    function complaint() {
		return $this->belongsTo('App\Model\Complaint', 'complaint_id','complaint_id');
	}

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
        if($this->assigned_to_source == "supplier"){ return $this->assignedToSeller(); }
        else if($this->assigned_to_source == "buyer"){ return $this->assignedToBuyer(); }
        else if($this->assigned_to_source == "user"){ return $this->assignedToUser(); }
        else if($this->assigned_to_source == "driver"){ return $this->assignedToDriver(); }
    }
  
    function assignedBySupplier() {
        return $this->belongsTo('App\Model\Supplier', 'assigned_by_id', 'supplier_id')->select(['supplier_id as assign_id', 'email',"fullname AS username"]);
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
        if($this->assigned_by_source == "supplier"){ return $this->assignedBySupplier(); }
        else if($this->assigned_by_source == "buyer"){ return $this->assignedByBuyer(); }
        else if($this->assigned_by_source == "user"){ return $this->assignedByUser(); }
        else if($this->assigned_by_source == "driver"){ return $this->assignedByDriver(); }
    }
}
