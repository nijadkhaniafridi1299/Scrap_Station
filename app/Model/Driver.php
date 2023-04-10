<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Model;
use App\Model\User;
use App\Validator\Driver as Validator;
use Illuminate\Support\Facades\Hash;
use App\Message\Error;

class Driver extends Model implements AuthenticatableContract ,JWTSubject //AuthorizableContract
{
    use Validator, Authenticatable,  HasFactory;
    protected $primaryKey = "driver_id";
    protected $table = "driver";
    protected $fillable = [
        'fullname', 'fullname_ar',
        'email', 'mobile', 'password', 'plain_password', 'gender', 'status', 'ip_address',
        'browser', 'is_logged_in', 'pass_change', 'created_at', 'updated_at', 'deleted_at', 'created_by',
        'profile_image', 'fcm_token_for_driver_app', 'fcm_token_for_web', 'notes', 'language', 'timezone',
        'location_id', 'address', 'sup_company_id',
		'iqama_cr_no', 'iqama_cr_file','is_verified','updated_source','updated_by',
        'last_login', 'auth_key','buyer_id' ,'valid_till', 'registration_date'
    ];
    protected $attributes = ['pass_change' => Null, 'status' => 9, 'last_login' => Null];
    public $timestamps = true;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'password', 'plain_password' ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    function buyer() {
        return $this->belongsTo('App\Model\Buyer','buyer_id', 'buyer_id')->select(['buyer_id','erp_id','fullname','email','mobile']);
	}
	function systemstatus() {
        return $this->belongsTo('App\Model\SystemStatus','status', 'id')->select(['id','key','name','name_ar','value','is_active']);
	}
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

	function addressdetail() {
		return $this->belongsTo('App\Model\Address', 'address_id', 'address_id')
		->select(['address_id','address', 'open_time', 'close_time', 'longitude', 'latitude', 'address_title', 'office_no']);
	}

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger( 'driver.add', $this->getErrors());
			return false;
		}
		
		$data['fullname'] = cleanNameString($data['fullname']);

		if (isset($data['mobile'])) {
			$data['mobile'] = (string) $data['mobile'];
			$data['mobile'] = trim($data['mobile']);
		}

		//$data['plain_password'] = (string) $data['plain_password'];
		if (isset($data['password'])) {
			$data['plain_password'] =  $data['password'];
			$data['password'] = Hash::make($data['password']);
		} else {
			Error::trigger('driver.add', ["Password is required"]);
			return false;
		}

		if (isset($data['old_password'])) {
			unset($data['old_password']);
		}

		if (isset($data['gender'])) {
			$data['gender'] = (string) $data['gender'];
		}

		if (isset($data['status'])) {
			$data['status'] = (int) $data['status'];
		}

		if (isset($data['last_login'])){
			$data['last_login'] = (string) $data['last_login'];
		}

		if (isset($data['pass_change'])) {
			$data['pass_change'] = (string) $data['pass_change'];
		}
        
		try {
		
			$user =  parent::add($data);
		
			return $user;//->toArray();
		}
		catch(\Exception $ex){
			Error::trigger("driver.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $driver_id, array $change_conditions = []){
         
		// if(!$this->validate($data)){
		// 	Error::trigger( 'driver.change', $this->getErrors());
		// 	return false;
		// }
		$driver = static::find($driver_id);

		if (isset($data['fullname'])) { $data['fullname'] = cleanNameString($data['fullname']); }
		else{ $data['fullname'] = $driver->fullname; }

		// if (!isset($data['fullname']) || $data['fullname'] == '') {
		// 	Error::trigger("driver.change", ["Please Enter Name in English/Arabic. Special Characters are not allowed."]);
		// 	return false;
		// }

		if (isset($data['gender'])) { $driver->gender = (string) $data['gender']; }

		if (isset($data['mobile']) && $data['mobile'] != '') {
			$driver->mobile = (int) $data['mobile'];
		}

		if (isset($data['status'])) {
			$driver->status = (int) $data['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($data['last_login'])) {
			$driver->last_login = (string) $data['last_login'];
		}

		if (isset($data['mobile'])) {
			$data['mobile'] = (string) $data['mobile'];
			$data['mobile'] = str_replace(' ', '', $data['mobile']);
		}	

		if(!isset($data['old_password'])){ $data['old_password'] = $driver->plain_password; }

		if (isset($data['pass_change'])) {
			$driver->password = Hash::make($data['pass_change']);
			$data['password'] = Hash::make($data['pass_change']);
			$data['plain_password'] = $data['pass_change'];
			$data['pass_change'] =  date("Y-m-d H:i:s");
		}
		else{
			if (isset($data['password'])) {
				$data['plain_password'] = $data['password'];
				$data['password'] = Hash::make($data['password']);
			} else if ($data['old_password']) {
				$data['plain_password'] = $data['old_password'];
				$data['password'] = Hash::make($data['old_password']);
			}
		}

		if(!isset($data['iqama_cr_no'])){ $data['iqama_cr_no'] = $driver->iqama_cr_no; }
		if(!isset($data['mobile'])){ $data['mobile'] = $driver->mobile; }

		unset($data['old_password']);

		// dd($data);
		try {
			// $change_conditions['email'] = "NULL,id,driver_id,".$driver_id; // additional where
			$change_conditions['email'] = $driver_id.",driver_id";
			$change_conditions['iqama_cr_no'] = $driver_id.",driver_id";
			$change_conditions['mobile'] = $driver_id.",driver_id";
			$driver = parent::change($data, $driver_id, $change_conditions);
			return $driver;
		} catch(Exception $ex) {
			Error::trigger("driver.change", [$ex->getMessage()]);
		}
  	}

    public static function getRoles($user_id) {
		$user = User::with('group')->where('user_id', $user_id)->first()->toArray();

		$roles = $user['role_id'];
		$userRoles = '';

		if ($roles == '[]') {
			return '';
		}

		if ($roles == '0') {
			return '';
		}

		return '';
	}

	static function refreshToken($token){
		$driver = Driver::where('auth_key',$token)->first();
		if($driver){
			$driver->valid_till = date("Y-m-d H:i:s", strtotime(date('Y/m/d H:i:s'))+604800);
			$driver->save();
			return 1;
		}
		else{
			return 0;
		}
	}
}
