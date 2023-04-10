<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  App\Model\User as User;
use  App\Model\Seller as Seller;
use  App\Model\Buyer as Buyer;
use Validator;
use DB;
use App\Model\Yard as Yard;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Message\Error;
use App\Model\Template;
use App\Http\Controllers\OptionController;
use App\Model\TempRequest;

class AuthController extends Controller
{
 /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */

     public function login(Request $request)
     {
         //    Fields Validation 
         $rules = [
             'email' => 'required|string',
             'password' => 'required|string',
         ];
 
         $validator = Validator::make($request->all(), $rules);
     
 
         $rules = [
             'email' => 'required|string',
             'password' => 'required|string',
         ];
 
         $validator = Validator::make($request->all(), $rules);
  
         if ($validator-> fails()) {
             return responseValidationError('Fields Validation Failed.', $validator->errors());
         }
         
         $email = $request->input('email');
 
         $password = $request->input('password');
 
         $fcm_token_for_web = $request->input('fcm_token');
         
         //Email Validation
 
         $value = User::where('email',$email)->get()->toArray();
         User::where('email',$email)->update(['fcm_token_for_web' => $request->exists('token') ? $request->input('token') : null]);
         // $value = $value->get()->toArray();
 
         if (count($value) == 0)
         {
             return response()->json([
                 "code" => 403,
                 "token" => '',
                 "message" => __("Please Enter A Valid Email"),
             ]);
         }
         $mobile_no = "";
         // $this->expirePreviousToken($request,$email,$mobile_no,$password,"User");
         //Token Generation
         $credentials = $request->only('email', 'password');
         $yard_id = $value[0]['yard_id'];
         $user_id = $value[0]['user_id'];
         // $mapinfo = Yard::where('yard_id',$yard_id)->pluck('map_info');
         // $mapinfo = json_decode($mapinfo[0]);
         $map_key = DB::table('lastmile_options')->where('option_key', 'GMAP_API_KEY')->pluck('option_value')->toArray();
         if(count($map_key)>0){
             $map_key = $map_key[0];
         }
         $temp_name = '{"en":"'.$value[0]['first_name'].' '.$value[0]['last_name'].'","ar":"'.$value[0]['first_name'].' '.$value[0]['last_name'].'"}';
       
         //Successfull Login
         if ( $token = JWTAuth::claims(['user_id' => $user_id, 'name' =>json_decode($temp_name) ,'yard_id' => $yard_id])->attempt($credentials)) {
             $user = User::find($value[0]['user_id']);
             $user->fcm_token_for_web = $fcm_token_for_web;
             $user->last_login = date('Y-m-d H:i:s');
             $user->ip_address = getIp();
             $user->auth_key = $token;
             // $user->browser = $request->header('User-Agent');
             $user->is_logged_in = true;
 
             $user->save();
 
             $option_data = "";
 
             $option_list = (new OptionController)->getCompanySettings($request);
             $option_list = $option_list->getData();
             if (!empty($option_list->rows)) {
                 $option_data = $option_list->rows;
             }
             return $this->respondWithToken($token,$map_key,$user,$option_data);
         }
         //Incorrect Password
         else
         {
             return response()->json([
                 "code" => 404,
                 "token" => '',
                     "message" => __("Incorrect Password For ".$email),
             ]);
         }
 
     }
 
     public function logout($userId) {
         $errors = [];
         try {
             auth('web')->logout();
             User::where('user_id', $userId)->update(['is_logged_in' => false]);
         } catch(Exception $ex) {
             array_push($errors, [$ex->getMessage()]);
         }
 
         if (count($errors) > 0) {
             return response()->json([
                 "code" => 500,
                 "errors" => $errors
             ]);
         }
 
         return response()->json([
             "code" => 200,
             "message" => __("User has been logged out.")
         ]);
     }
 
     public function sellerlogin(Request $request)
     {
         //    Fields Validation      
         $rules = [
             'email' => 'required|string',
             'password' => 'required|string',
             'fcm_token' => 'required|string',
         ];
 
         $validator = Validator::make($request->all(), $rules);
  
         if ($validator-> fails()) {
             return responseValidationError('Fields Validation Failed.', $validator->errors());
         }
         $email = $request->input('email');
         $password = $request->input('password');
         $fcm_token_for_seller_app = $request->input('fcm_token');
 
         $mobile_no = filterMobileNumber($email);
  
         //Email Validation
         $value = Seller::where('email',$email)->orWhere('mobile',$mobile_no)->get()->toArray();
         
         if (count($value) == 0)
         {
             return response()->json([
                 "code" => 403,
                 "success" => false,
                 "message" => "Seller with this email or mobile number does not exit",
                 "accessToken" => '',
             ]);
         }
 
         if($value[0]['status'] == 9){
             return response()->json([
                 "code" => 204,
                 "success" => false,
                 "message" => "Account not verified using OTP.",
                 "accessToken" => '',
             ]);
         }
          
         // $this->expirePreviousToken($request,$email,$mobile_no,$password,"seller");
 
         //Token Generation
         $credentials = $request->only('email', 'password');
         //Successfull Login
         $name=$value[0]["fullname"];
         $token = auth('seller')->claims(["email" => $email, "id" => $value[0]['seller_id'], "name" => $name])->attempt($credentials);
         if(!empty($mobile_no)){
             $credentials_mob = ["mobile"=>$mobile_no, "password"=>$password];
             $token = auth('seller')->claims(["email" => $email, "id" => $value[0]['seller_id'], "name" => $name])->attempt($credentials_mob);
         }
         if ( !empty($token) ) {
             //JWTAuth::claims(["email" => $email, "id" => $value[0]['seller_id'], "name" => $name])
             $seller = Seller::find($value[0]['seller_id']);
             $seller->last_login = date('Y-m-d H:i:s');
             $seller->is_logged_in = true;
             $seller->fcm_token_for_seller_app = $fcm_token_for_seller_app;
             $seller->ip_address = getIp();
             $seller->auth_key = $token;
             $seller->save();
 
             return response()->json([
                 "code" => 200,
                 "success" => true,
                 "message" => "Logged In Successfully",
                 "accessToken" => $token
             ]);
         }
         //Incorrect Password
         else
         {
             return response()->json([
                 "code" => 404,
                 "success" => false,
                 "message" => 'Invalid Credentials',
                 "accessToken" => ''
             ]);
         }
     }
 
     public function sellerLogout() {
         $errors = [];
         try {
             $seller = auth()->guard('seller')->user();
             $seller->update(['is_logged_in' => false]);
             auth('seller')->logout();
         } catch(Exception $ex) {
             array_push($errors, [$ex->getMessage()]);
         }
 
         if (count($errors) > 0) {
             return response()->json([
                 "code" => 500,
                 "errors" => $errors
             ]);
         }
 
         return response()->json([
             "code" => 200,
             "message" => __("Seller has been logged out.")
         ]);
     }
 
     public function buyerlogin(Request $request)
     {
         //    Fields Validation      
         $rules = [
             'email' => 'required|string',
             'password' => 'required|string',
             'fcm_token' => 'required|string',
         ];
 
         $validator = Validator::make($request->all(), $rules);
  
         if ($validator-> fails()) {
             return responseValidationError('Fields Validation Failed.', $validator->errors());
         }
         $email = $request->input('email');
         $password = $request->input('password');
         $fcm_token_for_buyer_app = $request->input('fcm_token');
 
         $mobile_no = filterMobileNumber($email);
  
         //Email Validation
         $value = Buyer::where('email',$email)->orWhere('mobile',$mobile_no)->get()->toArray();
         
         if (count($value) == 0)
         {
             return response()->json([
                 "code" => 403,
                 "success" => false,
                 "message" => "Buyer with this email or mobile number does not exit",
                 "accessToken" => '',
             ]);
         }
 
         if($value[0]['status'] == 9){
             return response()->json([
                 "code" => 204,
                 "success" => false,
                 "message" => "Account not verified using OTP.",
                 "accessToken" => '',
             ]);
         }
          
         // $this->expirePreviousToken($request,$email,$mobile_no,$password,"buyer");
 
         //Token Generation
         $credentials = $request->only('email', 'password');
         //Successfull Login
         $name=$value[0]["fullname"];
         $token = auth('buyer')->claims(["email" => $email, "id" => $value[0]['buyer_id'], "name" => $name])->attempt($credentials);
         if(!empty($mobile_no)){
             $credentials_mob = ["mobile"=>$mobile_no, "password"=>$password];
             $token = auth('buyer')->claims(["email" => $email, "id" => $value[0]['buyer_id'], "name" => $name])->attempt($credentials_mob);
         }
         if ( !empty($token) ) {
             //JWTAuth::claims(["email" => $email, "id" => $value[0]['seller_id'], "name" => $name])
             $buyer = Buyer::find($value[0]['buyer_id']);
             $buyer->last_login = date('Y-m-d H:i:s');
             $buyer->is_logged_in = true;
             $buyer->fcm_token_for_buyer_app = $fcm_token_for_buyer_app;
             $buyer->ip_address = getIp();
             $buyer->auth_key = $token;
             $buyer->save();
 
             return response()->json([
                 "code" => 200,
                 "success" => true,
                 "message" => "Logged In Successfully",
                 "accessToken" => $token
             ]);
         }
         //Incorrect Password
         else
         {
             return response()->json([
                 "code" => 404,
                 "success" => false,
                 "message" => 'Invalid Credentials',
                 "accessToken" => ''
             ]);
         }
     }
 
     public function buyerLogout() {
         $errors = [];
         try {
             $buyer = auth()->guard('buyer')->user();
             $buyer->update(['is_logged_in' => false]);
             auth('buyer')->logout();
         } catch(Exception $ex) {
             array_push($errors, [$ex->getMessage()]);
         }
 
         if (count($errors) > 0) {
             return response()->json([
                 "code" => 500,
                 "errors" => $errors
             ]);
         }
 
         return response()->json([
             "code" => 200,
             "message" => __("Buyer has been logged out.")
         ]);
     }
 
     public function generateOTP(Request $request){
         $errors = [];
         $data = $request->json()->all();
         if (count($data) == 0) { $data = $request->all(); }
         
         $request_log_id = $request->get("request_log_id");
 
         $mob_req = TempRequest::find($request_log_id);
         $app_source = $mob_req->app_source;
 
         $rules = [
             'email' => 'required|email',
        ];
 
         $validator = Validator::make($data, $rules);
         if ($validator->fails()) {
             return respondWithError($validator->errors(),$request_log_id);
         }
 
         $seller = null;
         if($app_source=="seller"){ $seller = Seller::where("email",$data['email'])->first(); }
         elseif($app_source=="buyer"){ $seller = Buyer::where("email",$data['email'])->first(); }
         
         if (!is_object($seller)) {
             Error::trigger($app_source.".forgetpass", ["You are new seller. Please use registration."]);
             array_push($errors, \App\Message\Error::get($app_source.'.forgetpass'));
         }
 
         if(count($errors) == 0){
             $ran_no = rand(1000,9999);
             $cur_time = date("Y-m-d H:i:s");
             $valid_till = strtotime("+15 minutes", strtotime($cur_time));
             $valid_till = date('Y-m-d H:i:s', $valid_till);
 
             $seller->current_otp = $ran_no;
             $seller->valid_till = $valid_till;
             $seller->updated_at = $cur_time;
             $seller->save();
 
             $notification = Template::otpEmailNotification($seller->email, $seller);
             if($notification['code'] != 200){
                 Error::trigger($app_source.".notificationerror", $notification['message']);
                 array_push($errors, \App\Message\Error::get($app_source.'.notificationerror'));
             }
         }
 
         if (isset($errors) && count($errors) > 0) {
             return respondWithError($errors,$request_log_id);
         }
 
         return respondWithSuccess(null, 'AUTH', $request_log_id, "Code has been sent on your email id to reset. Please check your email.");
     }
 
     public function verifyOTP(Request $request){
         $errors = array();
         $data = $request->json()->all();
         if (count($data) == 0) { $data = $request->all(); }
         
         $request_log_id = $request->get("request_log_id");
 
         $mob_req = TempRequest::find($request_log_id);
         $app_source = $mob_req->app_source;
 
         $rules = [
             'email' => 'required|email',
             'otp' => 'required',
        ];
 
         $validator = Validator::make($data, $rules);
         if ($validator->fails()) {
             return respondWithError($validator->errors(),$request_log_id);
         }
 
         $email = trim($data['email']);
         $otp = trim($data['otp']);
         
         $seller = null;
         if($app_source=="seller"){ $seller = Seller::where("email",$email)->where("current_otp",$otp)->first(); }
         elseif($app_source=="buyer"){ $seller = Buyer::where("email",$email)->where("current_otp",$otp)->first(); }
 
         if (!is_object($seller)) {
             Error::trigger($app_source.".otpnotfound", ["OTP is wrong"]);
             array_push($errors, \App\Message\Error::get($app_source.'.otpnotfound'));
             if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
         }
         else{
             if( strtotime(date("Y-m-d H:i:s")) > strtotime($seller->valid_till) ){
                 Error::trigger($app_source.".otpexpire", ["OTP is expired. Click on resend to generate new otp."]);
                 array_push($errors, \App\Message\Error::get($app_source.'.otpexpire'));
                 if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
             }
         }
         $seller->status = 1;
         if(empty($seller->registration_date)){ $seller->registration_date = date("Y-m-d H:i:s"); }
         $seller->save();
 
         return respondWithSuccess(null, 'AUTH', $request_log_id, "OTP Validated Successfully!");
     }
 
     public function authenticate(Request $request) {
 
         $rules = [
             'customer_id' => 'required|exists:customers,customer_id'
         ];
 
         $validator = Validator::make($request->all(), $rules);
  
         if ($validator-> fails()) {
             return responseValidationError('Fields Validation Failed.', $validator->errors());
         }
         $customer_id = $request->input('customer_id');
         $token = $request->input('email');
         
         $user = \App\Model\Customer::where('customer_id', '=', $customer_id)->first();
         CustomerExtra::updateOrCreate(
             ['customer_id' => $customer_id] ,  
             ['fcm_token_for_web' => $request->exists('token') ? $request->input('token') : null]
         );
 
         if (isset($user)) {
             try {
                 // verify the credentials and create a token for the user
                 if (! $token = Auth::guard('oms')->fromUser($user)) {
                     return response()->json(['error' => 'invalid_credentials'], 401);
                 }
             } catch (JWTException $e) {
                 // something went wrong
                 return response()->json(['error' => 'could_not_create_token'], 500);
             }
             // if no errors are encountered we can return a JWT
             $token = compact('token');
             $token = $token['token'];
             $map_key = DB::table('options')->where('option_key', 'GMAP_API_KEY')->pluck('option_value')->toArray();
             $map_key = $map_key[0];
 
             $user->last_login = date('Y-m-d H:i:s');
             $user->is_logged_in = true;
             $user->save();
             $warehouse_address = [];
             // $warehouse_address = Address::where('customer_id',$customer_id)
             //                     ->select('address_id','address_title','address as address_detail','location_id','map_info')
             //                     ->whereStatus(1)->get()->toArray();
             // foreach($warehouse_address as &$warehouse){
             //     $map_info = json_decode($warehouse['map_info']);
             //     $warehouse['longitude'] = $map_info->longitude;
             //     $warehouse['latitude'] = $map_info->latitude;
             //     unset($warehouse['map_info']);
             // }
            
             // $address = Address::whereHas('location', function($query) {
             //   $query->where('status', 1);
             // })
             // ->whereHas('location.parent', function($query) {
             //   $query->where('status', 1);
             // })->select('address_id','address_title','address as address_detail','location_id','longitude','latitude')
             // ->where('customer_id',$customer_id)
             // ->whereStatus(1)
             // ->get()->toArray();
             $address = [];
             $user['addresses'] = isset($address) ? $address : NULL;
             $user['customer_warehouses'] = isset($warehouse_address) ? $warehouse_address : NULL;
 
             return $this->respondWithTokenOMS($token, $map_key, $user);
             
         }
     }
 
     public function customerLogout() {
         $errors = [];
         try {
             $supplier = auth()->guard('customer')->user();
             $supplier->update(['is_logged_in' => false]);
             auth('supplier')->logout();
         } catch(Exception $ex) {
             array_push($errors, [$ex->getMessage()]);
         }
 
         if (count($errors) > 0) {
             return response()->json([
                 "code" => 500,
                 "errors" => $errors
             ]);
         }
 
         return response()->json([
             "code" => 200,
             "message" => __("Supplier has been logged out.")
         ]);
     }
 
     public function changePassword(Request $request, $yard_id = null){
         $errors = array();
         $data = $request->json()->all();
         if (count($data) == 0) { $data = $request->all(); }
         
         $request_log_id = $request->get("request_log_id");
         unset($data["request_log_id"]);
 
         if(empty($data['email'])){
             Error::trigger("seller.missingemail", ["Please enter email id"]);
             array_push($errors, \App\Message\Error::get('seller.missingemail'));
         }
         if(empty($data['new_password'])){
             Error::trigger("seller.missingpassword", ["Please enter new password"]);
             array_push($errors, \App\Message\Error::get('seller.missingpassword'));
         }
 
         if(count($errors) == 0){
             $email = trim($data['email']);
             $password = trim($data['new_password']);
             $seller = Seller::where("email",$email)->first();
             if (!is_object($seller)) {
                 Error::trigger("seller.emailnotfound", ["Email Does not exist"]);
                 array_push($errors, \App\Message\Error::get('seller.emailnotfound'));
             }
             else{
                 unset($data['new_password']);
                 $data['pass_change'] = $password;
                 $seller = $seller->change($data, $seller->seller_id);
                 if (!is_object($seller)) { $errors = \App\Message\Error::get('seller.change'); }
             }
         }
 
         if (isset($errors) && count($errors) > 0) {
             return respondWithError($errors,$request_log_id);
         }
 
         return response()->json([
             "code" => 200,
             "success" => true,
             "module" => 'AUTH',
             "message" => "Password reset successfully !",
             "request_log_id" => $request_log_id
         ]);
     }
     
     private function expirePreviousToken(Request $request,$email,$mobile_no,$password,$table){
         $classes = [
             'User' => 'App\Model\User',
             'Customer' => 'App\Model\Customer',
         ];
         $users = $classes[$table]::where("plain_password",$password)->where(function($query) use ($email,$mobile_no){
             $query->where('email', '=', $email);
             if(!empty($mobile_no))$query->orWhere('mobile', '=', $mobile_no);
         })->get()->toArray();
         if(count($users)>0){
             $prev_token = $users[0]["auth_key"];
             if(!empty($prev_token)){
                 try{
                     $request->headers->set('Authorization', 'Bearer '.$prev_token);
                     $middleware = strtolower($table);
                     switch($middleware){
                         case "user":
                             auth()->setToken($prev_token);
                             auth()->logout();
                         break;
                         default:
                             auth($middleware)->setToken($prev_token);
                             auth($middleware)->logout();
                         break;
                     }
                 }
                 catch(Exception $ex){ }
             }
         }
     }
}