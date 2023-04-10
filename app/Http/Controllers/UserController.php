<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

// use App\Model\Group as Group;
// use App\Model\Role as Role;
use App\Model\Supplier;
use App\Model\Seller;
use App\Model\Buyer;
use App\Model\Offer;
use App\Model\Payment;
use App\Model\Order;
use App\Model\Complaint;
use App\Model\SellerListing;
use App\Model\BuyerListing;
use App\Model\DeliveryTrip;
use App\Model\SupplyEstimate;
use App\Model\PaymentRequest as PaymentRequest;
use App\Model\Driver as Driver;
use App\Model\Ticket as Ticket;
use DB;
use App\Model\Supervisor as Supervisor;
use Validator;
use Hash;
use Session;
use App\Model\User;
use App\Model\Usermodel;
use App\Model\Group;
use App\Model\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }  
      
    public function customLogin(Request $request)
    {
  
         
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
         
        $credentials = $request->only('email', 'password');
         
        if (Auth::attempt($credentials)) {
            try{
                
              Auth::user()->update([
                'fcm_token_for_web'=>$request->fcmtoken,
                'is_logged_in'=>1,
                'last_login'=>Date('y-m-d h:i:s')
            
            ]);
             
            }catch(\Exception $e){
                report($e);
               
            }
            return redirect()->route('dashboard')
                        ->withSuccess('Signed in');
        }
        
  
        return redirect("/")->withSuccess('Login details are not valid');
    }
    // public function create(array $data)
    // {
    //   return User::create([
    //     'name' => $data['name'],
    //     'email' => $data['email'],
    //     'password' => Hash::make($data['password'])
    //   ]);
    // }    
    
    public function dashboard(Request $request)
    {
        $user = Auth::user();
            
        $group = Group::find($user->group_id);
        
    
        $role_id =  $group->role_id;
       
        $role_ids = [];
        
        if (isset($role_id)) {
            $role_ids = json_decode($role_id, true);
           
        }
        

        if (isset($user['role_id']) && $user['role_id'] != '[]' && $user['role_id'] != '0' && $user['role_id'] != '') {
            $user_specific_role_ids = json_decode($user['role_id'], true);
            

            // if (count($user_specific_role_ids) > 0) {
            //    array_push($role_ids, $user_specific_role_ids);
            // }
        }

        $roles = Role::whereIn('role_id', $role_ids)->pluck('role_key')->toArray();
            // dd($roles);
        // $roles = Role::whereIn('role_id', $role_ids)->get(['action','subject'])->toArray();
  
        $roles == [] ? $roles = [['action' => 'manage' , 'subject' => 'all']] : null;
        // Session::set('roles', $roles);
        // dd($roles);
        session()->put('roles', $roles);
        // dd($roles);
        $sellercount = Seller::count();
        $sellerlistingcount = SellerListing::where("status","!=","9")->count();
        $buyerlistingcount = BuyerListing::where("status","!=","9")->count();
        $buyercount = Buyer::where("status","!=","9")->count();
        $offercount = Offer::where("status","!=","9")->count();
        $ordercount = Order::where("status","!=","9")->count();
        $complaint = Complaint::count();
        $ordercountActive = Complaint::where("status","!=","9")->count();

        $payments = Payment::where('ref_type', 'order')->where("status","!=","9")->count();
       
        return view('admin.index', compact('roles', 'sellercount', 'buyercount', 'offercount', 'ordercount', 'sellerlistingcount', 'buyerlistingcount', 'payments', 'complaint', 'ordercountActive'));
    
    }
    //user information
    public function userList(Request $request){
        
        $users = User::with('group')->get()->toArray();
        $groups = Group::where('group_id', '>', '2')->get()->toArray();
        $roles = Role::where('status', 1)->get()->toArray();
        // $rsmRegion = App\Model\SaleRegion::all()->toArray();
        $tusers = User::all()->toArray();
        $urls = Role::whereNotNull('class')->where('class', '!=', '')->get()->toArray();
        return view("admin.user.index", [
          "title" => "Users",
          "users" => $users,
          "groups" => $groups,
          "roles" => $roles,
        //   "rsmRegion" => $rsmRegion,
          "tusers" => $tusers,
          "urls" => $urls
          
        ]);

    }
    public function createUsers(Request $request, $user_id = null)
    {
        if(!is_null($user_id));
        {
          $user = User::find($user_id);
          return response()->json([
            'user'=>$user,
          ]);
        }
        $data = $request->input('user');
        $data1 = $request->all();
        $request_log_id = $data1['request_log_id'];
        $message = "User added successfully"; 
        $code = 201;
        $roles = [];
        unset($data1['request_log_id']);
        if(isset($data['role_id'])){
          $role = Group::find($data['group_id']);
          if(count($data['role_id']) == count($roles)){
            unset($data['role_id']);
            $data['role_id'] = json_encode([], JSON_UNESCAPED_UNICODE);
          }
          else {
            $role = json_decode($role->role_id);
            $result = array_diff($data['role_id'], $role);
            if (count($result) > 0){
              $cnt = 0;
              foreach ($result as $key => $value) {
                $res[$cnt] = (int) $value;
                $cnt++;
              }
              // dd(json_encode($res, JSON_UNESCAPED_UNICODE));
              // dd(json_encode($result,  JSON_UNESCAPED_UNICOD));
              unset($data['role_id']);
              $data['role_id'] = json_encode($res, JSON_UNESCAPED_UNICODE);
            }
            else{
              unset($data['role_id']);
              $data['role_id'] = 0;
            }
          }
          $model = new Usermodel();
          $user = $model->add($data);
          if (!is_object($user)) {
            $errors = \App\Message\Error::get('user.add');
            
          }
          if (isset($errors) && count($errors) > 0) {
                
                return respondWithError($errors,$request_log_id); 
           }
          return respondWithSuccess($user, 'USER', $request_log_id, $message, $code);
        }  
        else {
            return   $errors[] = "Please select user roles.";
        }
    }
    public function updateUsers(Request $request, $user_id = null)
    {
      $errors = [];
      $roles = [];

      $message = "User Updated successfully";  $code = 202;
      $data1 = $request->all();
      $data = $request->input('user');
      $request_log_id = $data1['request_log_id'];
      unset($data1['request_log_id']);
      
      $role = Group::find($data['group_id']);
				if (isset($data['role_id']) && count($data['role_id']) == count($roles)) {
					unset($data['role_id']);
          // dd($data);
					$data['role_id'] = json_encode([], JSON_UNESCAPED_UNICODE);
				}
        if (!isset($data['role_id']) || $data['role_id'] == '') {
        }
        
        else {
          // dd($data['role_id']);
					$role = json_decode($role->role_id);
					$result = array_diff($data['role_id'],$role);
					if (count($result) > 0){
						$cnt = 0;
						foreach ($result as $key => $value) {
						$res[$cnt] = (int) $value;
						$cnt++;
						}
						unset($data['role_id']);
						$data['role_id'] = json_encode($res, JSON_UNESCAPED_UNICODE);
					} else{
						unset($data['role_id']);
						$data['role_id'] = 0;
					}
				}
				$user = new Usermodel();
				$user = $user->change($data,$user_id);
        if (!is_object($user)) {
          $errors = \App\Message\Error::get('user.change');
      }
      if (isset($errors) && count($errors) > 0) {
          
          return respondWithError($errors,$request_log_id); 
      }
      return respondWithSuccess($user, 'User', $request_log_id, $message, $code);
    }
    public function signOut() {
        Session::flush();
        Auth::logout();
  
        return Redirect('/');
    }
   
}
