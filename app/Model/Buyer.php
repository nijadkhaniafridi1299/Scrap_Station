<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Model;
use App\Model\User;
use App\Validator\Buyer as Validator;
use Illuminate\Support\Facades\Hash;
use App\Message\Error;

class Buyer extends Model implements AuthenticatableContract,JWTSubject //AuthorizableContract
{
    use Validator, Authenticatable, HasFactory;
    protected $primaryKey = "buyer_id";
    protected $table = "buyer";
    protected $fillable = [
        'erp_id','fullname','fullname_ar','email','mobile','password','plain_password','gender','iqama_cr_no','is_verified','promo_code_used','refferal_code','wallet_amount','deal_in_progress','deal_completed','status','ip_address','browser','is_logged_in','pass_change','username','profile_image','fcm_token_for_buyer_app','fcm_token_for_web','notes','timezone','address_id','last_login','auth_key','valid_till','current_otp','registration_date','updated_source','updated_at','updated_by'
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

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

	function listings() {
        return $this->hasMany('App\Model\BuyerListing','buyer_id', 'buyer_id');
	}
	function driver() {
        return $this->hasMany('App\Model\Driver','buyer_id', 'buyer_id');
	}
	function image() {
        return $this->hasMany('App\Model\Image','ref_id', 'buyer_id')->where('ref_type','buyer_iqama')->select(['img_id', 'ref_id', "path"]);
	}

	function systemstatus() {
        return $this->belongsTo('App\Model\SystemStatus','status', 'id')->select(['id','key','name','name_ar','value','is_active']);
	}

    function add($data) {

		if(!$this->validate($data)){
			Error::trigger( 'buyer.add', $this->getErrors());
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
			Error::trigger('buyer.add', ["Password is required"]);
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
			Error::trigger("buyer.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $buyer_id, array $change_conditions = []){

		// if(!$this->validate($data)){
		// 	Error::trigger( 'buyer.change', $this->getErrors());
		// 	return false;
		// }
		$buyer = static::find($buyer_id);

		if (isset($data['fullname'])) { $data['fullname'] = cleanNameString($data['fullname']); }
		else{ $data['fullname'] = $buyer->fullname; }

		// if (!isset($data['fullname']) || $data['fullname'] == '') {
		// 	Error::trigger("buyer.change", ["Please Enter Name in English/Arabic. Special Characters are not allowed."]);
		// 	return false;
		// }

		if (isset($data['gender'])) { $buyer->gender = (string) $data['gender']; }

		if (isset($data['mobile']) && $data['mobile'] != '') {
			$buyer->mobile = (int) $data['mobile'];
		}

		if (isset($data['status'])) {
			$buyer->status = (int) $data['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($data['last_login'])) {
			$buyer->last_login = (string) $data['last_login'];
		}

		if (isset($data['mobile'])) {
			$data['mobile'] = (string) $data['mobile'];
			$data['mobile'] = str_replace(' ', '', $data['mobile']);
		}	

		if(!isset($data['old_password'])){ $data['old_password'] = $buyer->plain_password; }

		// if (!isset($data['pass_change'])){
		// 	$buyer->password = $data['old_password'];
		// }
		if (isset($data['pass_change'])) {
			$buyer->password = Hash::make($data['pass_change']);
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

		if(!isset($data['iqama_cr_no'])){ $data['iqama_cr_no'] = $buyer->iqama_cr_no; }

		unset($data['old_password']);

		// dd($data);
		try {
			// $change_conditions['email'] = "NULL,id,buyer_id,".$buyer_id; // additional where
			$change_conditions['email'] = $buyer_id.",buyer_id";
			$change_conditions['iqama_cr_no'] = $buyer_id.",buyer_id";
			$change_conditions['mobile'] = $buyer_id.",buyer_id";
			$buyer = parent::change($data, $buyer_id, $change_conditions);
			return $buyer;
		} catch(Exception $ex) {
			Error::trigger("buyer.change", [$ex->getMessage()]);
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
		$buyer = Buyer::where('auth_key',$token)->first();
		if($buyer){
			$buyer->valid_till = date("Y-m-d H:i:s", strtotime(date('Y/m/d H:i:s'))+604800);
			$buyer->save();
			return 1;
		}
		else{
			return 0;
		}
	}
}
