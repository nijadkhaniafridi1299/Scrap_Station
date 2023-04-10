<?php

namespace App\Model;


use App\Model;
use App\Model\User;
use App\Validator\User as Validator;
use Illuminate\Support\Facades\Hash;
use App\Message\Error;

class Usermodel extends Model
{
    use Validator;
    protected $primaryKey = "user_id";
    protected $table = "users";
    protected $fillable = [
        'erp_id', 
        'yard_id', 
        'first_name', 
		'first_name_ar', 
        'last_name', 
		'last_name_ar', 
        'email', 
		'mobile',
        'password', 
        'is_logged_in', 
        'plain_password', 
        'gender', 
        'group_id', 
        'ip_address',
        'pass_change',
        'role_id',
        'profile_image',
        'fcm_token_for_web',
        'location_id',
		'last_login',
		'status',
		'auth_key',
    ];
    protected $attributes = ['group_id'=>Null, 'pass_change' => Null, 'status' => 1, 'last_login' => Null, 'role_id' => Null];
    public $timestamps = true;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password','plain_password','pass_change'];


    function add($data) {

		if(!$this->validate($data)){
			Error::trigger( 'seller.add', $this->getErrors());
			return false;
		}

		$data['first_name'] = cleanNameString($data['first_name']);

		if (!isset($data['first_name']) || $data['first_name'] == '') {
			Error::trigger("seller.add", ["Please Enter First name in English/Arabic. Special Characters are not allowed."]);
			return false;
		}

		$data['last_name'] = cleanNameString($data['last_name']);

		if (!isset($data['last_name']) || $data['last_name'] == '') {
			Error::trigger("seller.add", ["Please Enter Last Name in English/Arabic. Special Characters are not allowed."]);
			return false;
		}
		if (isset($data['email'])) {
			$data['email'] = (string) $data['email'];
		}

		//$data['plain_password'] = (string) $data['plain_password'];
		if (isset($data['password'])) {
			$data['plain_password'] =  $data['password'];
			$data['password'] = Hash::make($data['password']);
		}

		if (isset($data['gender'])) {
			$data['gender'] = (int) $data['gender'];
		}

		// if (isset($data['avatar'])) {
		// 	$data['avatar'] = (string) $data['avatar'];
		// }

		if (isset($data['group_id'])) {
			$data['group_id'] = (int) $data['group_id'];
		} else {
			Error::trigger('seller.add', ["User Roles are not specified."]);
			return false;
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
			Error::trigger("seller.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $user_id,  array $change_conditions = []){
		$user = static::find($user_id);
		$data['first_name'] = cleanNameString($data['first_name']);

		if (!isset($data['first_name']) || $data['first_name'] == '') {
			Error::trigger("user.change", ["Please Enter First name in English/Arabic. Special Characters are not allowed."]);
			return false;
		}

		$data['last_name'] = cleanNameString($data['last_name']);

		if (!isset($data['last_name']) || $data['last_name'] == '') {
			Error::trigger("user.change", ["Please Enter Last Name in English/Arabic. Special Characters are not allowed."]);
			return false;
		}

		

		if (isset($data['email'])){
			if ($user->email != $data['email']) {
				$user->email = (string) $data['email'];
			} else {
				unset($data['email']);
			}
		} else {
			Error::trigger('user.change', ["Email is required."]);
			return false;
		}

		if (isset($data['gender'])){
			$user->gender = (string) $data['gender'];
		} else {
			Error::trigger('user.change', ["Gender is required."]);
			return false;
		}


		if (isset($data['group_id'])) {
			$user->group_id = (int) $data['group_id'];
		} else {
			Error::trigger('user.change', ["User Group is required."]);
			return false;
		}


		if (isset($data['status'])) {
			$user->status = (int) $data['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($data['last_login'])) {
			$user->last_login = (string) $data['last_login'];
		}
	
		if (isset($data['pass_change'])) {
			$user->password = Hash::make($data['pass_change']);
			$data['password'] = Hash::make($data['pass_change']);
			$data['plain_password'] = $data['pass_change'];
		}

		// if (!isset($data['pass_change'])){
		// 	$user->password = $data['old_password'];
		// }

		if (isset($data['role_id'])){
			$user->role_id = (string) $data['role_id'];
		} else {
			Error::trigger('user.change', ["User Roles are not specified."]);
			return false;
		}

		if (isset($data['url'])) {
			$user->url = (string) $data['url'];
		}
    

		unset($data['old_password']);
		unset($data['pass_change']);
		
		try {
			// dd($data);
			//$user->save();
			$user = parent::change($data, $user_id);
			return $user;
		} catch(Exception $ex) {
			Error::trigger("user.change", [$ex->getMessage()]);
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

		// $role_ids = json_decode($roles, true);

		// $role_names = \App\Model\Role::whereIn('role_id', $role_ids)->pluck('role_name');

		// $names = $role_names->toArray();

		// return implode(", ", $names);

		return '';
	}

	// static function refreshToken($token){
	// 	$seller = Seller::where('auth_key',$token)->first();
	// 	if($seller){
	// 		$seller->valid_till = date("Y-m-d H:i:s", strtotime(date('Y/m/d H:i:s'))+604800);
	// 		$seller->save();
	// 		return 1;
	// 	}
	// 	else{
	// 		return 0;
	// 	}
	// }
}
