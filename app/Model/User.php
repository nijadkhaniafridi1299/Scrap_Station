<?php

namespace App\Model;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Model;
use App\Validator\User as Validator;
// use Illuminate\Support\Facades\Hash;
use App\Message\Error;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
	protected $appends = ['fullname','fullname_ar'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password','plain_password','pass_change'];
    
    function getFullnameAttribute() {
		return $this->first_name." ".$this->last_name;
	}

	function getFullnameArAttribute() {
		return $this->first_name_ar." ".$this->last_name_ar;
	}

	function group() {
		return $this->belongsTo('App\Model\Group', 'group_id', 'group_id')->where('status', 1);
	}

	function vehicle() {
		return $this->hasOne('App\Model\Vehicle', 'driver_id', 'user_id')->where('status', 1);
	}
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

}
