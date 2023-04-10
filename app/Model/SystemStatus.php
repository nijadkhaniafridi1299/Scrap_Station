<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;
use App\Message\Error;

class SystemStatus extends Model
{
	protected $primaryKey = 'id';
	protected $table = 'system_statuses';
	protected $fillable = ['key','name','name_ar','value','is_active','created_source','created_at','created_by','updated_source','updated_at','updated_by'];
    protected $hidden = ['is_active','created_source','created_at','created_by','updated_source','updated_at','updated_by','deleted_at'];
	public $timestamps = true;

    function add($data) {
        try {
			$system_status =  parent::add($data);
			return $system_status;
		}
		catch(\Exception $ex){
			Error::trigger("system_status.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $status_id, array $change_conditions = []) {
        try {
			$system_status = parent::change($data, $status_id, $change_conditions);
			return $system_status;
		} catch(Exception $ex) {
			Error::trigger("system_status.change", [$ex->getMessage()]);
		}
    }
}
