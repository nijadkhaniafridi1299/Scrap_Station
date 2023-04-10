<?php

namespace App\Model;

use App\Model;
use App\Validator\Checkpoint as Validator;

class Checkpoint extends Model
{
    use Validator;
    protected $primaryKey = "chkpt_id";
    protected $table = "checkpoint";
    protected $fillable = ['erp_id','name','name_ar','description','status','created_source','created_by','created_at','updated_source','updated_by','updated_at'];
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

    function image() {
        return $this->belongsTo('App\Model\Image', 'chkpt_id','ref_id')->where('ref_type','checkpoint')->select(['img_id', 'ref_id',"path"]);
	}

    function add($data) {
        
        try {
			$checkpoint =  parent::add($data);
			return $checkpoint;
		}
		catch(\Exception $ex){
			Error::trigger("checkpoint.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $chkpt_id, array $change_conditions = []) {
        try {
			$checkpoint = parent::change($data, $chkpt_id, $change_conditions);
			return $checkpoint;
		} catch(Exception $ex) {
			Error::trigger("checkpoint.change", [$ex->getMessage()]);
		}
    }
}
