<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\Seller;
use App\Model\Buyer;
use App\Model\User;

use App\Model\SellerListing;
use App\Model\BuyerListing;
use App\Model\SystemStatus;
use App\Model\MaterialCategory;
use App\Model\Material;
use App\Http\Controllers\OptionController;
use App\Model\SellerListingApplicant;
use App\Model\BuyerListingApplicant;
use App\Model\Checkpoint;
use App\Model\Offer;
use App\Model\Order;
use App\Model\Payment;
use App\Model\Address;
use App\Model\Driver;
use App\Message\Error;
use App\Model\Group;
use App\Model\Role;
use App\Model\Notification;
use Illuminate\Support\Facades\Auth;

use DB;
use Validator;
use Hash;
use Session;

class BuyerController extends Controller
{   
    
     // Web Controller Functions
    public function getBuyerDetailsWeb(Request $request, $id=null){
        
        $data = $request->all();
        $system_status = SystemStatus::where('key', 'LIKE', "%".'ACTIVE_INACTIVE'."%")->get();
        $type = ['individual'=>"Individual",'establishment'=>"Establishment",'contractor'=>"Contractor",'corporate'=>"Corporate"];

        if(!is_null($id)){
          $buyer = Buyer::with(['systemstatus'])->where('buyer_id', $id)->orderBy("created_at","DESC")->first();
          
          return response()->json([
            'status'=>200,
            'buyer'=>$buyer,
          ]);
        }
       
        $buyers = Buyer::with(['systemstatus']);
        if (isset($data['fullname']) && $data['fullname'] != null && $data['fullname'] != "") {
            $buyers->where('fullname', 'LIKE', "%".$data['fullname']."%");
        }
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $buyers->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
        } 
        if (isset($data['type']) && $data['type'] != null && $data['type'] != "") {
            $buyers->where('type', 'LIKE', "%".$data['type']."%");
        }
        if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
            $buyers->where('is_verified', $data['is_verify']);
          }   
        $buyers = $buyers->orderBy("created_at","DESC")->get();
     
       return view('admin.buyer_details', compact('buyers', 'data', 'system_status', 'type'));
    }
    public function getBuyerDashboardWeb($id = null){
        $buyer = Buyer::find($id);
        $driver= Driver::with('buyer', 'systemstatus')->where('buyer_id', $id)->get();
        $buyerAddress =  \DB::select (" select  distinct(address) from addresses a inner join buyer_listing bl on bl.address_id=a.address_id where bl.buyer_id=$id");
    //    dd($buyerAddress);
          
        // return $buyerAddress[0]->listings[0]->buyer_list_id;
        $systemstatus = SystemStatus::where('key', 'LIKE', "%".'LISTING'."%")->get();
        $listings = BuyerListing::with(['address:address_id,address','material','buyer','material','applicants','applicants.offers', 'systemstatus'])->where("buyer_id",$id)->where("status",'!=',9)->orderBy("created_at","DESC")->get();
        $orders = Order::with(["address","driver","material.image","offer.buyer","offer.seller","offer.sellerlisting","offer.sellerlisting.images"])->whereHas("offer.buyer", function($query) use($id){
            $query->where('buyer_id', $id);
        })
        ->orderBy("order_id", "DESC")->get();
      
        $payments = Payment::with(["order:order_id,order_no,total_price,price,offer_id,mat_id,driver_id,quantity,quantity_unit,total_price","order.driver","order.material:mat_id,sub_cat_id,product_code,name,name_ar,description","order.material.image","order.offer:offer_id,offer_no,seller_id,buyer_id,offered_price,offered_price_with_vat,reason,status","order.offer.buyer","order.offer.seller"])->where("status","!=","9")
        ->whereHas("order.offer.buyer",function($q) use ($id){
            $q->where('buyer_id', $id);
        });
        $payments = $payments->orderBy("status","ASC")->orderBy("created_at","DESC")->get();
       
        return view('admin.buyer_dashboard',compact('buyer', 'listings', 'orders', 'payments', 'systemstatus', 'driver', 'buyerAddress'));
    }
    public function chang_buyer_statusweb(Request $request){
        $id = $request->input('id');
      
        $buyer=Buyer::find($id);
        $buyer->update([
            'is_verified' =>  $buyer->is_verified == 0 ? 1 : 0,
            'updated_source'=> 'user',
            'updated_at'=>  date("Y-m-d H:i:s"),
            'updated_by'=> Auth::user()->user_id,

        ]);
       
        return response()->json([
            'status'=>200,
            'buyer'=>$buyer,
             
        ]);
    }
    public function buyer_Updated(Request $request){
        $id = $request->input('buyer_id');
    
        $validator = Validator::make([
                    
            'buyer_id' => $id
        ],[
            'buyer_id' => 'int|min:1|exists:buyer,buyer_id',
  
        ]);
        if ($validator-> fails()){
            return responseValidationError('Fields Validation Failed.', $validator->errors());
        }

        $errors = [];
           
            $data = $request->all();
          
            // $request_log_id = $data['request_log_id'];
            // unset($data['request_log_id']);

            $buyer= new Buyer();
            $buyer = $buyer->change($data, $id);

            if (!is_object($buyer)) {
                $errors = \App\Message\Error::get('buyer.change');
            } 

            if (count($errors) > 0) {
                return response()->json([
                    "code" => 500,
                    "errors" => $errors
                ]);
            }


            return response()->json([
                    'status'=>200,
                    'message'=>'Buyer Updated Successfully',
                ]);
    
    
        // $seller = Seller::find($id);
        // $seller->update([
        //     'fullname'=>isset($data['name']),
        //     'fullname_ar'=>isset($data['name_arc']),
        //     'iqama_cr_no'=>isset($data['iqama_cr_no']),
        //     'email'=>isset($data['email']),
        //     'mobile'=>isset($data['mobileno']),
        //     'status'=>isset($data['status_id']),
           
        // ]);

        // return response()->json([
        //     'status'=>200,
        //     'message'=>'Seller Updated Successfully',
        // ]);
    }
    public function buyer_listingweb(Request $request, $id = null){
        $data = $request->all();
        $system_status = SystemStatus::where('key', 'LIKE', "%".'LISTING'."%")->get();
        $buyerlistings = BuyerListing::with(['address:address_id,address','material','buyer','material','applicants','applicants.offers', 'systemstatus']);
        if(!is_null($id)){
            $buyerlistings = BuyerListing::with(['address:address_id,address','material','buyer','material','applicants','applicants.offers', 'systemstatus'])->where("status",'!=',9)->where('buyer_list_id', $id)->orderBy("created_at","DESC")->first();
            $images = $buyerlistings->images;
            return response()->json([
                'status'=>200,
                'buyerlistings'=>$buyerlistings,
                'images'=>$images 
            ]);
        }
        if (isset($data['fullname']) && $data['fullname'] != null && $data['fullname'] != "") {
            $fullname = $data['fullname'];
            $buyerlistings->whereHas('buyer', function($q) use($fullname){
                $q->where('fullname', 'LIKE', "%".$fullname."%");
              });
          
        }
        if (isset($data['mname']) && $data['mname'] != null && $data['mname'] != "") {
            $mname = $data['mname'];
            $buyerlistings->whereHas('material', function($q) use($mname){
                $q->where('name', 'LIKE', "%".$mname."%");
              });
          
        }
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $buyerlistings->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
        }
        if(isset($data['date']) && $data['date'] != null && $data['date'] != ""){
            $date= $data['date'];
            // dd($date);
            $buyerlistings->whereDate('created_at','=',$date);
          }   
          if(isset($data['day']) && $data['day'] != null && $data['day'] != ""){
            $day= $data['day'];
            // dd($date);
            $buyerlistings->where('active_days','=',$day);
          }
        if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
            $buyerlistings->where('is_verified', $data['is_verify']);
          }    
          $buyerlistings = $buyerlistings->where("status",'!=',9)->orderBy("created_at","DESC")->get();        
          return view('admin.buyerListing', compact('buyerlistings', 'system_status', 'data'));
       
    }
    public function chang_buyerListing_statusweb(Request $request){
        $data = $request->all();
        $id = $request->input('id');
      
        $buyerlisting = BuyerListing::find($id);
        $buyer = Buyer::find($buyerlisting->buyer_id);
        $buyerlisting->update([
            'is_verified' =>  $buyerlisting->is_verified == 0 ? 1 : 0,
            'updated_source' => $data['created_source'],
            'updated_by' => $data['created_by'],
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        // Notification Work Zeeshan Qureshi
        $notification_id = $buyer->fcm_token_for_buyer_app;

        $notification_message = "Buyer Listing # " .$buyerlisting->listing_no." is Approved.";
        $source = 1;
        $is_sent = 0;

        if ($notification_id != "" || $notification_id != null) {
            $title = "Buyer Listing Update";
            $type = "basic";
            try {
                $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        }

        $insertNotification = Notification::insert([
            'to_source' => "buyer",
            'user_id' => $buyer->buyer_id,
            'reference_type' => 'Buyer Listing',
            'reference_id' => $buyerlisting->buyer_list_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            'created_source' => $data['created_source'],
            'created_by' => $data['created_by'],
            'created_at' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'status'=>200,
            'buyerlisting'=>$buyerlisting,
             
        ]);
    }
   public function add_wallet_amount(Request $request, $id = null)
   {
      $data = $request->all();
      $buyer = Buyer::find($id);
      $wallet_amount = $buyer->wallet_amount+$data['wallet_amount'];

      $buyer->update([
        'wallet_amount' =>$wallet_amount,
        'updated_source'=> 'user',
        'updated_at'=>  date("Y-m-d H:i:s"),
        'updated_by'=> Auth::user()->user_id,
      ]);
      return response()->json([
       'code'=>200,
       'buyer'=>$buyer,
      ]);
   }
    public function get_applicant(Request $request, $buyer_list_id=null){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        // $request_log_id = $data['request_log_id'];
        // unset($data['request_log_id']);

        $res_data = array();
        $code = 200; $message = "Offers Loaded Successfully !";
        // DB::enableQueryLog();
        $offer_arr = Offer::where("status","!=","9")->where("listing_source","buyer_listing");
        if(!is_null($buyer_list_id)){ $offer_arr = $offer_arr->where("listing_id",$buyer_list_id); }
        $offer_arr = $offer_arr->groupBy("application_source")->groupBy("application_id")->pluck('application_id')->toArray();

        $offers = array();

        foreach($offer_arr as $app_id){
            $temp_offer = Offer::with(['buyerlisting','buyer','seller', 'order'])->where("status","!=","9")->where("listing_source","buyer_listing");
            $temp_offer = $temp_offer->where("application_id",$app_id)->orderBy("created_at","DESC")->orderBy("listing_id","DESC")->get();
        //    dd(  $temp_offer );
            array_push($offers, $temp_offer[0]);
        }
        // dd($offers);
        // dd(DB::getQueryLog());
        if(count($offers)==0){ $code = 204; $message = "No Offers Available!"; }

        $res_data['offers'] = $offers;
        // dd($res_data);
        return response()->json([
            'status'=>200,
            'applicants'=>$res_data
        ]);

    }
     // Mobile Controller Functions
     public function create(Request $request)
     {
         $errors = [];
         $data = $request->json()->all();//$request->all();
 
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
         $buyer = new Buyer();
 
         //step1: create a new buyer in buyers
         $data['buyer']['created_at'] =  date('Y-m-d H:i:s');
 
         if(isset($data['created_by'])){ $data['buyer']['created_by'] = $data['created_by']; }
         if(isset($data['created_source'])){ $data['buyer']['created_source'] = $data['created_source']; }
 
         $data['buyer'] = sanitizeData($data['buyer']);

         if(!empty($data['buyer']['mobile']) && is_numeric($data['buyer']['mobile'])){
            $data['buyer']['mobile'] = filterMobileNumber($data['buyer']['mobile']);
         }

         $buyer = new Buyer();
         $validated_buyer = $buyer->validateAtStart($data['buyer']);
 
         if (!$validated_buyer ) {
             array_push($errors, \App\Message\Error::get('buyer.start'));
         }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         $buyer = $buyer->add($data['buyer']);
 
         if (!is_object($buyer)) {
             $errors = \App\Message\Error::get('buyer.add');
         }
 
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         $getSuppName=$data['buyer']['fullname'];
 
         $get_Users=User::join('groups','users.group_id','=','groups.group_id')
             ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();
 
         if(count($get_Users)>0)
         {
         for($i=0;$i<count($get_Users);$i++)
         {
         $notification_id=$get_Users[$i]['fcm_token_for_web'];
         $source = 0;
         $is_sent = 0;
         $notification_message = "Buyer " .$getSuppName." wants to register" ;
 
         if ($notification_id != "" || $notification_id != null) {
             $title = "Buyer Registration";
             $type = "basic";
            try {
                $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
         } 
         $insertNotification=Notification::insert([
             'to_source' => "user",
             'user_id'=>$get_Users[$i]['user_id'],
             'reference_type' => 'buyer',
             'reference_id' => $buyer->buyer_id,
             'notification_body' => $notification_message,
             'is_sent' => $is_sent,
         //  'created_source' => $data->created_source,
         //  'created_by' => $data->created_by,
         ]);
         }
         }
 
         return respondWithSuccess($buyer, 'BUYER', $request_log_id, "Your registration request sent to Scrap Station for Approval.", 201);
     }
 
     public function updateBuyer(Request $request, $buyer_id)
     {
         $errors = [];
         $data = $request->json()->all();//$request->all();
 
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
 
         $data['buyer_id'] = $buyer_id;
 
         $validator = Validator::make($data,[
             'buyer_id'  => 'required|int|min:1|exists:buyer,buyer_id',
         ]);
 
         if($validator->fails()){
             array_push($errors, $validator->errors()->first()); return respondWithError($errors,$request_log_id);
         }
 
         $buyer = Buyer::find($buyer_id);
         $data['buyer']['updated_at'] =  date('Y-m-d H:i:s');
         if(isset($data['created_by'])){ $data['buyer']['updated_by'] = $data['created_by']; }
         if(isset($data['created_source'])){ $data['buyer']['updated_source'] = $data['created_source']; }
 
         $buyer = $buyer->change($data['buyer'],$buyer_id);
         if (!is_object($buyer)) { $errors = \App\Message\Error::get('buyer.change'); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         if(isset($data['images']) && count($data['images'])>0){
             foreach($data['images'] as $img_path){
                 $img_data = prepareImageData("buyer_iqama",$img_path,$data,"Buyer Iqama Information");
                 $buyer->image()->create($img_data);
             }
         }
 
         $getSuppName=$data['buyer']['fullname'];
 
         $get_Users=User::join('groups','users.group_id','=','groups.group_id')
             ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();
 
         if(count($get_Users)>0)
         {
         for($i=0;$i<count($get_Users);$i++)
         {
             $notification_id=$get_Users[$i]['fcm_token_for_web'];
             $source = 0;
             $is_sent = 0;
             $notification_message = "Buyer " .$getSuppName." Updated Information" ;
 
             if ($notification_id != "" || $notification_id != null) {
                 $title = "Buyer Update";
                 $type = "basic";
                 try {
                    $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                    $is_sent = 1;
                 } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
             } 
             $insertNotification=Notification::insert([
                 'to_source' => "user",
                 'user_id'=>$get_Users[$i]['user_id'],
                 'reference_type' => 'buyer',
                 'reference_id' => $buyer->buyer_id,
                 'notification_body' => $notification_message,
                 'is_sent' => $is_sent,
            //  'created_source' => $data->created_source,
            //  'created_by' => $data->created_by,
             ]);
             }
         }
 
         return respondWithSuccess($buyer, 'BUYER', $request_log_id, "Your Information is submitted to update in Scrap Station.", 204);
     }
 
     public function createListing(Request $request){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
 
         $res_data = array();
 
         $data['buyer_listing'] = sanitizeData($data['buyer_listing']);
         $buyer_listing_data = $data['buyer_listing'];
         $buyer_listing_data['buyer_id'] = $data['created_by'];
         $buyer_listing_data['created_source'] = $data['created_source'];
         $buyer_listing_data['created_by'] = $data['created_by'];
         $buyer_listing_data['created_at'] = date("Y-m-d H:i:s");
         $buyer_listing_data['status'] = 7;
 
         $address_id = null;
 
         if($data['address_detail'] != null){
             $data['address_detail'] = sanitizeData($data['address_detail']);
             $address_data = array();
             $address_data['latitude'] = $data['address_detail']['lat'];
             $address_data['longitude'] = $data['address_detail']['lan'];
             $address_data['address'] = $data['address_detail']['address'];
             $address_obj = Address::where("latitude",$address_data['latitude'])->where("longitude",$address_data['longitude'])->where("created_by",$data['created_by'])->where("created_source",$data['created_source'])->select("address_id")->get();
             if(count($address_obj)==0){
                 $address_data['created_source'] = $data['created_source'];
                 $address_data['created_by'] = $data['created_by'];
                 $address_data['created_at'] = date("Y-m-d H:i:s");
                 $address_obj = new Address();
                 $validated_address_obj = $address_obj->validateAtStart($address_data);
 
                 if (!$validated_address_obj ) { array_push($errors, \App\Message\Error::get('address.start')); }
                 if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
                 $address_obj = $address_obj->add($address_data);
             }
             else{ $address_obj = $address_obj[0]; }
             $address_id = $address_obj->address_id;
         }
 
         if($address_id != null){ $buyer_listing_data['address_id'] = $address_id; }
 
         $listing_no = generateNumber("BuyerListing", "buyer_id", $data['created_by']);
 
         $buyer_listing_data['listing_no'] = $listing_no;
 
         $buyer_listing = new BuyerListing();
         $validated_buyer_listing = $buyer_listing->validateAtStart($buyer_listing_data);
 
         if (!$validated_buyer_listing ) { array_push($errors, \App\Message\Error::get('buyerlisting.start')); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         $buyer_listing = $buyer_listing->add($buyer_listing_data);
 
         if (!is_object($buyer_listing)) { $errors = \App\Message\Error::get('buyerlisting.add'); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         if(isset($data['images']) && count($data['images'])>0){
             foreach($data['images'] as $img_path){
                 $img_data = prepareImageData("buyer_listing",$img_path,$data);
                 $buyer_listing->images()->create($img_data);
             }
         }
         $res_data['buyer_listing'] = $buyer_listing;
 
         // $subcat_obj = $buyer_listing->material->subcategory;
         // $subcat_obj->current_avl_quantity += $seller_listing->quantity;
         // $subcat_obj->save();
         $get_Users = User::join('groups','users.group_id','=','groups.group_id')
        ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

        $notification_message = "Request for Buyer Listing " .$buyer_listing->listing_no." is generated. Please check and verify." ;

        if(count($get_Users)>0)
        {
            for($i=0; $i<count($get_Users); $i++)
            {
                $notification_id = $get_Users[$i]['fcm_token_for_web'];
                $source = 0;
                $is_sent = 0;
                if ($notification_id != "" || $notification_id != null) {
                    $title = "Buyer Listing Request";
                    $type = "basic";
                    try {
                            $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                            $is_sent = 1;
                    } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                }
                $insertNotification = Notification::insert([
                    'to_source' => "user",
                    'user_id' => $get_Users[$i]['user_id'],
                    'reference_type' => 'Buyer Listing',
                    'reference_id' => $buyer_listing->buyer_list_id,
                    'notification_body' => $notification_message,
                    'is_sent' => $is_sent,
                    'created_source' => $data['created_source'],
                    'created_by' => $data['created_by'],
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
        }
 
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, "Thank you for using service. You will be contacted from CSR for verfication of this listing.",201);
     }
 
     public function showListing(Request $request,$buyer_list_id=null){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
 
         $res_data = array();
         $listings = null; $code = 200; $message = "List Loaded Successfully!";
 
         if(!is_null($buyer_list_id)){
             $listings = BuyerListing::with(['images','address','material','material.image','applicants','applicants.seller','applicants.offers'])->where("created_source",$data['created_source'])->where("created_by",$data['created_by'])->where("buyer_list_id",$buyer_list_id)->where("status",'!=',9)->orderBy("created_at","DESC")->get();
             if(count($listings)>0){
                 $res_data['listing'] = $listings[0];
                 $message = "Record Loaded Successfully!";
             }
             else{
                 $res_data['listing'] = [];
                 $message = "No Specific Listing Found !";
             }
         }
         else{
             $listings = BuyerListing::with(['images','address','material','material.image','applicants','applicants.seller','applicants.offers'])->where("created_source",$data['created_source'])->where("created_by",$data['created_by'])->where("status",'!=',9)->orderBy("created_at","DESC")->get();
             $res_data['listings'] = $listings;
         }
         
         if(count($listings)==0){ $code = 204; $message = "No Record Found!"; }
 
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, $message, $code);
     }
 
     public function getListingOffers(Request $request,$buyer_list_id){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
 
         $res_data = array();
         $code = 200; $message = "Offers Loaded Successfully !";
 
         $offers = Offer::with(['buyerlisting','buyer'])->where("status","!=","9")->where("listing_source","buyer_listing")->where("listing_id",$buyer_list_id)->where("buyer_id",$data['created_by'])->orderBy("created_at","DESC")->get();
         $res_data['offers'] = $offers;
 
         if(count($offers)==0){ $code = 204; $message = "No Offers Found Against This Listing !"; }
 
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, $message, $code);
     }
 
     public function getLatestOffers(Request $request,$buyer_list_id = null){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
 
         $res_data = array();
         $code = 200; $message = "Offers Loaded Successfully !";
 
         $offer_arr = Offer::where("status","!=","9")->where("listing_source","buyer_listing")->where("buyer_id",$data['created_by']);
         if(!is_null($buyer_list_id)){ $offer_arr = $offer_arr->where("listing_id",$buyer_list_id); }
         $offer_arr = $offer_arr->groupBy("application_source")->groupBy("application_id")->pluck('application_id')->toArray();
 
         $offers = array();
 
         foreach($offer_arr as $app_id){
             $temp_offer = Offer::with(['buyerlisting','buyer','seller'])->where("status","!=","9")->where("listing_source","buyer_listing")->where("buyer_id",$data['created_by']);
             $temp_offer = $temp_offer->where("application_id",$app_id)->orderBy("created_at","DESC")->orderBy("listing_id","DESC")->get();
             array_push($offers, $temp_offer[0]);
         }
         // dd($offers);
 
         if(count($offers)==0){ $code = 204; $message = "No Offers Available!"; }
 
         $res_data['offers'] = $offers;
 
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, $message, $code);
     }
 
     public function closeListing(Request $request, $buyer_list_id){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
         $code = 204; $message = "Listing is closed Now !";
 
         $data['buyer_list_id'] = $buyer_list_id;
 
         $validator = Validator::make($data,[
             'buyer_list_id'  => 'required|int|min:1|exists:buyer_listing,buyer_list_id',
             'closed_reason'  => 'required'
          ]);
          if($validator->fails()){
             array_push($errors, $validator->errors()->first());
             return respondWithError($errors,$request_log_id);
          }
 
         $buyer_listing = BuyerListing::find($buyer_list_id);
         $buyer_listing->status = 8;
         $buyer_listing->closed_reason = $data['closed_reason'];
         $buyer_listing->save();
 
         // price deduction and refund
 
 
         // remove all offers of closed listing
         $buyer_listing->offers()->update(['status' => 9]);
 
         return respondWithSuccess(null, 'BUYER', $request_log_id, $message, $code);
     }
 
     public function getDashboardDetails(Request $request){
         $errors = [];
         $data = $request->json()->all();//$request->all();
 
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
 
         $res_data = array();
         $buyer_id = $data['buyer_id'];
 
         $buyer = Buyer::find($buyer_id);
 
         if (!is_object($buyer)) {
             Error::trigger("buyer.dashboard", ["buyer does not exist."]);
             array_push($errors, \App\Message\Error::get('buyer.dashboard'));
         }
 
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         $res_data = array();
         $buyer_data = array();
 
         $buyer_data['buyer_id'] = $buyer_id;
         $buyer_data['buyer_name'] = $buyer->fullname;
         $buyer_data['email'] = $buyer->email;
         $buyer_data['mobile'] = $buyer->mobile;
         $buyer_data['registration_date'] = $buyer->registration_date;
         $buyer_data['iqama_cr_no'] = $buyer->iqama_cr_no;
         $buyer_data['refferal_code'] = $buyer->refferal_code;
         $buyer_data['wallet_amount'] = $buyer->wallet_amount;
         $buyer_data['deal_in_progress'] = $buyer->deal_in_progress;
         $buyer_data['deal_completed'] = $buyer->deal_completed;
         $buyer_data['listing_count'] = count($buyer->listings);
         // $buyer_data['iqama_cr_file'] = $buyer->iqama_cr_file;
 
         $address = null;
         
         if($buyer->addressdetail != null){
           $address = Buyer::with('addressdetail')->where('buyer_id',$buyer_id)->get();
           $address =  $address[0]->addressdetail;
         }
       
         $buyer_data['location'] = null;
         if(!empty($address->latitude)){
             $buyer_data['location']['lat'] = $address->latitude;
             $buyer_data['location']['lan'] = $address->longitude;
             $buyer_data['location']['address'] = $address->address;
         }
 
         $res_data["buyer"] = $buyer_data;
 
         $option_data = "";
         $option_list = (new OptionController)->getCompanySettings($request);
         $option_list = $option_list->getData();
         if (!empty($option_list->rows)) {
             $option_data = $option_list->rows;
         }
         $res_data["option"] = $option_data;
 
         $system_statuses = SystemStatus::orderBy("key")->get();
         $res_data["system_statuses"] = $system_statuses;
 
         $material_categories = MaterialCategory::with(["image","subcategories","subcategories.image","subcategories.materials","subcategories.materials.image"])->where("status","1")
         ->whereHas("subcategories",function($q){})
         ->whereHas("subcategories.materials",function($q){})
         ->get();
         $res_data["material_categories"] = $material_categories;
 
         $checkpoints = Checkpoint::with(["image"])->where("status","!=",9)->get();
         $res_data['checkpoints'] = $checkpoints;
 
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, "");
     }
 
     public function getSellerCategoriesInformation(Request $request){
         $errors = [];
         $data = $request->json()->all();//$request->all();
 
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
 
         $res_data = array();
 
         $material_categories = MaterialCategory::with(["image"])->where("status","1")->get();
 
         $mat_cats = MaterialCategory::with(["subcategories"])->where("status","1")->get();
 
         foreach($mat_cats as $cat_ky => $cat){
             $list_count = 0;
             if($cat->subcategories != null){
                 foreach($cat->subcategories as $subcat){
                     if($subcat->materials != null){
                         foreach($subcat->materials as $mat){
                             if($mat->verifiedsellerlistings != null){ $list_count += count($mat->verifiedsellerlistings); }
                         }
                     }
                 }
             }
             
             $material_categories[$cat_ky]['listings'] = $list_count;
         }
 
         $res_data["material_categories"] = $material_categories;
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, "");
     }
 
     public function getMaterialsOfSubcategory(Request $request,$cat_id){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
         $code = 200; $message = "List Loaded Successfully!";
 
         $res_data = array();
 
         $seller_listings = SellerListing::with(['images','address','material','material.image'])->where("status","7")->where("is_verified",1);
         $seller_listings = $seller_listings->whereHas('material.subcategory.category', function($query) use ($cat_id){
             $query->where('cat_id', $cat_id);
         });
         $seller_listings = $seller_listings->orderBy("created_at","DESC")->get();
         $res_data['seller_listings'] = $seller_listings;
 
         if(count($seller_listings)==0){ $code = 204; $message = "No Listings Found!"; }
 
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, $message, $code);
     }
 
     public function createOffer(Request $request){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
         $code = 201; $message = "Offer Request Generated. You will be contacted by CSR for verification.";
 
         $buyer_wallet = 0;
 
         $buyer_obj = null;
 
         if($data['created_source'] == "buyer"){
             $buyer_obj = Buyer::find($data['created_by']); $buyer_wallet = $buyer_obj->wallet_amount;
         }
 
         if($data['offer']['offered_price_with_vat'] > $buyer_wallet){
             return respondWithError(['You do not have enough amount for offer. Please Recharge your wallet.'], $request_log_id);
         }
 
         $data['seller_list_applicant'] = sanitizeData($data['seller_list_applicant']);
 
         $sell_list_applicant_data = $data['seller_list_applicant'];
         $sell_list_applicant_data['buyer_id'] = $data['created_by'];
         $sell_list_applicant_data['created_by'] = $data['created_by'];
         $sell_list_applicant_data['created_source'] = $data['created_source'];
         $sell_list_applicant_data['created_at'] = date("Y-m-d H:i:s");
         $sell_list_applicant_data['sell_list_id'] = $data['offer']['listing_id'];
 
         $seller_list = SellerListing::find($data['offer']['listing_id']);

         $seller = Seller::find($seller_list->seller_id);
         $seller_notification_id = $seller->fcm_token_for_seller_app;
         $offer_id = "";
         $seller_id = $seller->seller_id;
 
         $offer_data = $data['offer'];
         $offer_data['seller_id'] = $seller_list->seller_id;
         $offer_data['buyer_id'] = $data['created_by'];
         $offer_data['created_by'] = $data['created_by'];
         $offer_data['created_source'] = $data['created_source'];
         $offer_data['created_at'] = date("Y-m-d H:i:s");
         $offer_data['listing_source'] = "seller_listing";
 
         $offer = new Offer();
         $validated_offer = $offer->validateAtStart($offer_data);
 
         if (!$validated_offer ) { array_push($errors, \App\Message\Error::get('offer.start')); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         $address_id = null;
 
         if($data['address_detail'] != null){
             $data['address_detail'] = sanitizeData($data['address_detail']);
             $address_data = array();
             $address_data['latitude'] = $data['address_detail']['lat'];
             $address_data['longitude'] = $data['address_detail']['lan'];
             $address_data['address'] = $data['address_detail']['address'];
             $address_obj = Address::where("latitude",$address_data['latitude'])->where("longitude",$address_data['longitude'])->where("created_by",$data['created_by'])->where("created_source",$data['created_source'])->select("address_id")->get();
             if(count($address_obj)==0){
                 $address_data['created_source'] = $data['created_source'];
                 $address_data['created_by'] = $data['created_by'];
                 $address_data['created_at'] = date("Y-m-d H:i:s");
                 $address_obj = new Address();
                 $validated_address_obj = $address_obj->validateAtStart($address_data);
 
                 if (!$validated_address_obj ) { array_push($errors, \App\Message\Error::get('address.start')); }
                 if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
                 $address_obj = $address_obj->add($address_data);
             }
             else{ $address_obj = $address_obj[0]; }
             $address_id = $address_obj->address_id;
         }
 
         if($address_id != null){ $offer_data['address_id'] = $address_id; }
         
         $application_id = null;
         $applicant_obj = SellerListingApplicant::where("sell_list_id",$data['offer']['listing_id'])->where("created_by",$data['created_by'])->where("created_source",$data['created_source'])->get();
         if(count($applicant_obj)==0){
             $sell_list_applicant = new SellerListingApplicant();
             $validated_sell_list_applicant = $sell_list_applicant->validateAtStart($sell_list_applicant_data);
 
             if (!$validated_sell_list_applicant ) { array_push($errors, \App\Message\Error::get('sellerlistingapplicant.start')); }
             if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
             $application_no = generateNumber("SellerListingApplicant", "buyer_id", $data['created_by']);
             $sell_list_applicant_data['sell_list_app_no'] = $application_no;
             $sell_list_applicant = $sell_list_applicant->add($sell_list_applicant_data);
             if (!is_object($sell_list_applicant)) { $errors = \App\Message\Error::get('sellerlistingapplicant.add'); }
             if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
             $application_id = $sell_list_applicant->sell_list_app_id;
         }
         else{ $application_id = $applicant_obj[0]->sell_list_app_id; }
 
         $previous_offer_id = null; $type = "new";
 
         $prev_offer = Offer::where("status","!=","9")->where("listing_source","seller_listing")->where("listing_id",$data['offer']['listing_id'])->where("created_by",$data['created_by'])->where("created_source",$data['created_source'])->orderBy("created_at","DESC")->take(1)->get();
         if(count($prev_offer)>0){
             $prev_offer = $prev_offer[0];
             $previous_offer_id = $prev_offer->offer_id;
             $type = "updated";
             $prev_offer->status = 5;
             $prev_offer->save();
         }
 
         $offer_no = generateNumber("Offer", "buyer_id", $data['created_by']);
         $offer_data['offer_no'] = $offer_no;
         $offer_data['previous_offer_id'] = $previous_offer_id;
         $offer_data['type'] = $type;
         $offer_data['application_source'] = "seller_listing_applicant";
         $offer_data['application_id'] = $application_id;
         $offer = $offer->add($offer_data);
         
         if (!is_object($offer)) { $errors = \App\Message\Error::get('offer.add'); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

         $offer_id = $offer->offer_id;
 
         $message .= "Reference # ".$offer_no;
 
         if($type == "new" && !is_null($buyer_obj)){
             $buyer_obj->wallet_amount -= $offer->offered_price_with_vat;
             $buyer_obj->save();
             $pay_no = "B-".generateNumber("Payment", "created_by", $data['created_by']);
             $pay_data = [
                 'pay_no' => $pay_no,
                 'ref_type' => 'offer',
                 'ref_id' => $offer->offer_id,
                 "pay_amount" => $offer->offered_price_with_vat,
                 "created_source" => $data['created_source'],
                 "created_by" => $data['created_by'],
                 "created_at" => date("Y-m-d H:i:s")
             ];
             $pay_obj = new Payment();
             $pay_obj->add($pay_data);
         }

         // Notification Work Zeeshan Qureshi
         $get_Users=User::join('groups','users.group_id','=','groups.group_id')
         ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

         $notification_message = "Offer # " .$offer->offer_no." is generated on Seller Listing # ".$seller_list->listing_no ;

         if(count($get_Users)>0)
         {
             for($i=0; $i<count($get_Users); $i++)
             {
                 $notification_id = $get_Users[$i]['fcm_token_for_web'];
                 $source = 0;
                 $is_sent = 0;
                 if ($notification_id != "" || $notification_id != null) {
                     $title = "Offer Generated";
                     $type = "basic";
                     try {
                         $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                         $is_sent = 1;
                     } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                 }
                 $insertNotification = Notification::insert([
                     'to_source' => "user",
                     'user_id' => $get_Users[$i]['user_id'],
                     'reference_type' => 'Offer',
                     'reference_id' => $offer->offer_id,
                     'notification_body' => $notification_message,
                     'is_sent' => $is_sent,
                     'created_source' => $data['created_source'],
                     'created_by' => $data['created_by'],
                     'created_at' => date("Y-m-d H:i:s")
                 ]);
             }
         }
         
        // notification for seller
        $source = 1;
        $is_sent = 0;
        if ($seller_notification_id != "" || $seller_notification_id != null) {
            $title = "Offer Update";
            $type = "basic";
            try {
                $res = send_notification_FCM($seller_notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        }
        $insertNotification = Notification::insert([
            'to_source' => "seller",
            'user_id' => $seller_id,
            'reference_type' => 'Offer',
            'reference_id' => $offer_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            'created_source' => $data['created_source'],
            'created_by' => $data['created_by'],
            'created_at' => date("Y-m-d H:i:s")
        ]);

         return respondWithSuccess(null, 'BUYER', $request_log_id, $message, $code);
     }
 
     public function createBuyerListingOffer(Request $request){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
         $code = 201; $message = "Offer Request Generated. You will be contacted by CSR for verification.";
 
         $buyer_wallet = 0;
 
         $buyer_obj = null;
 
         $application_id = $data['buyer_list_applicant_id'];
 
         if($data['created_source'] == "buyer"){
             $buyer_obj = Buyer::find($data['created_by']); $buyer_wallet = $buyer_obj->wallet_amount;
         }
 
         if($data['offer']['offered_price_with_vat'] > $buyer_wallet){
             return respondWithError(['You do not have enough amount for offer. Please Recharge your wallet.'], $request_log_id);
         }
         
         $buyer_list_applicant = BuyerListingApplicant::find($application_id);
 
         $offer_data = $data['offer'];
         $offer_data['seller_id'] = $buyer_list_applicant->seller_id;
         $offer_data['buyer_id'] = $data['created_by'];
         $offer_data['created_by'] = $data['created_by'];
         $offer_data['created_source'] = $data['created_source'];
         $offer_data['created_at'] = date("Y-m-d H:i:s");
         $offer_data['listing_source'] = "buyer_listing";
 
         $offer = new Offer();
         $validated_offer = $offer->validateAtStart($offer_data);
 
         if (!$validated_offer ) { array_push($errors, \App\Message\Error::get('offer.start')); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         $address_id = null;
 
         if($data['address_detail'] != null){
             $data['address_detail'] = sanitizeData($data['address_detail']);
             $address_data = array();
             $address_data['latitude'] = $data['address_detail']['lat'];
             $address_data['longitude'] = $data['address_detail']['lan'];
             $address_data['address'] = $data['address_detail']['address'];
             $address_obj = Address::where("latitude",$address_data['latitude'])->where("longitude",$address_data['longitude'])->where("created_by",$data['created_by'])->where("created_source",$data['created_source'])->select("address_id")->get();
             if(count($address_obj)==0){
                 $address_data['created_source'] = $data['created_source'];
                 $address_data['created_by'] = $data['created_by'];
                 $address_data['created_at'] = date("Y-m-d H:i:s");
                 $address_obj = new Address();
                 $validated_address_obj = $address_obj->validateAtStart($address_data);
 
                 if (!$validated_address_obj ) { array_push($errors, \App\Message\Error::get('address.start')); }
                 if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
                 $address_obj = $address_obj->add($address_data);
             }
             else{ $address_obj = $address_obj[0]; }
             $address_id = $address_obj->address_id;
         }
 
         if($address_id != null){ $offer_data['address_id'] = $address_id; }
 
         $previous_offer_id = null; $type = "new";
 
         $prev_offer = Offer::where("status","!=","9")->where("listing_source","buyer_listing")->where("listing_id",$data['offer']['listing_id'])->where("created_by",$data['created_by'])->where("created_source",$data['created_source'])->orderBy("created_at","DESC")->take(1)->get();
         if(count($prev_offer)>0){
             $prev_offer = $prev_offer[0];
             $previous_offer_id = $prev_offer->offer_id;
             $type = "updated";
             $prev_offer->status = 5;
             $prev_offer->save();
         }
 
         $offer_no = generateNumber("Offer", "buyer_id", $data['created_by']);
         $offer_data['offer_no'] = $offer_no;
         $offer_data['previous_offer_id'] = $previous_offer_id;
         $offer_data['type'] = $type;
         $offer_data['application_source'] = "buyer_listing_applicant";
         $offer_data['application_id'] = $application_id;
         $offer = $offer->add($offer_data);
         
         if (!is_object($offer)) { $errors = \App\Message\Error::get('offer.add'); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
 
         $message .= "Reference # ".$offer_no;
 
         if($type == "new" && !is_null($buyer_obj)){
             $buyer_obj->wallet_amount -= $offer->offered_price_with_vat;
             $buyer_obj->save();
             $pay_no = "B-".generateNumber("Payment", "created_by", $data['created_by']);
             $pay_data = [
                 'pay_no' => $pay_no,
                 'ref_type' => 'offer',
                 'ref_id' => $offer->offer_id,
                 "pay_amount" => $offer->offered_price_with_vat,
                 "created_source" => $data['created_source'],
                 "created_by" => $data['created_by'],
                 "created_at" => date("Y-m-d H:i:s")
             ];
             $pay_obj = new Payment();
             $pay_obj->add($pay_data);
         }

        // Notification Work Zeeshan Qureshi
        $get_Users=User::join('groups','users.group_id','=','groups.group_id')
         ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

         $notification_message = "Offer # " .$offer->offer_no." is generated on Buyer Listing # ".$offer->listing->listing_no;

        if(count($get_Users)>0)
        {
            for($i=0; $i<count($get_Users); $i++)
            {
                $notification_id = $get_Users[$i]['fcm_token_for_web'];
                $source = 0;
                $is_sent = 0;
                if ($notification_id != "" || $notification_id != null) {
                    $title = "Offer Generated";
                    $type = "basic";
                    try {
                        $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                        $is_sent = 1;
                    } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                }
                $insertNotification = Notification::insert([
                    'to_source' => "user",
                    'user_id' => $get_Users[$i]['user_id'],
                    'reference_type' => 'Offer',
                    'reference_id' => $offer->offer_id,
                    'notification_body' => $notification_message,
                    'is_sent' => $is_sent,
                    'created_source' => $data['created_source'],
                    'created_by' => $data['created_by'],
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
        }
 
         return respondWithSuccess(null, 'BUYER', $request_log_id, $message, $code);
     }
 
     public function getOffers(Request $request, $offer_id = null){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
         $code = 200; $message = "Offers Loaded Successully !";
 
         $res_data = array();
         $offers = array();
 
         if(is_null($offer_id)){
             $offer_arr = Offer::where("status","!=","9")->where("listing_source","seller_listing")->where("buyer_id",$data['created_by']);
             $offer_arr = $offer_arr->groupBy("application_source")->groupBy("application_id")->pluck('application_id')->toArray();
             foreach($offer_arr as $app_id){
                 $temp_offer = Offer::with(['buyer','seller','sellerlisting','sellerlisting.address','sellerlisting.material','sellerlisting.material.image','sellerlisting.images'])->where("status","!=","10")->where("listing_source","seller_listing")->where("buyer_id",$data['created_by']);
                 $temp_offer = $temp_offer->where("application_id",$app_id)->orderBy("created_at","DESC")->orderBy("listing_id","DESC")->get();
                 if(count($temp_offer)>0) { array_push($offers, $temp_offer[0]); }
             }
             $res_data['offers'] = $offers;
         }
         else{
             $offers = Offer::with(['buyer','seller','sellerlisting','sellerlisting.address','sellerlisting.material','sellerlisting.material.image','sellerlisting.images'])->where("status","!=","9")->where("created_source",$data['created_source'])->where("created_by",$data['created_by']);
             $offers = $offers->where("offer_id",$offer_id)->orderBy("created_at","DESC")->get();
             if(count($offers)>0){ $res_data['offer'] = $offers[0]; $message = "Offer Loaded Successully !"; }
         }
         if(count($offers)==0){ $code = 204; $message = "No Record Found !"; }
 
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, $message, $code);
     }
 
     public function getBuyerListOffers(Request $request, $offer_id = null){
         $errors = [];
         $data = $request->json()->all();//$request->all();
         
         $request_log_id = $data['request_log_id'];
         unset($data['request_log_id']);
         $code = 200; $message = "Offers Loaded Successully !";
 
         $res_data = array();
         $offers = array();
 
         if(is_null($offer_id)){
             $offer_arr = Offer::where("status","!=","9")->where("listing_source","buyer_listing")->where("buyer_id",$data['created_by']);
             $offer_arr = $offer_arr->groupBy("application_source")->groupBy("application_id")->pluck('application_id')->toArray();
             foreach($offer_arr as $app_id){
                 $temp_offer = Offer::with(['buyer','seller','buyerlisting','buyerlisting.address','buyerlisting.material','buyerlisting.material.image','buyerlisting.images'])->where("status","!=","10")->where("listing_source","buyer_listing")->where("buyer_id",$data['created_by']);
                 $temp_offer = $temp_offer->where("application_id",$app_id)->orderBy("created_at","DESC")->orderBy("listing_id","DESC")->get();
                 if(count($temp_offer)>0) { array_push($offers, $temp_offer[0]); }
             }
             $res_data['offers'] = $offers;
         }
         else{
             $offers = Offer::with(['buyer','seller','buyerlisting','buyerlisting.address','buyerlisting.material','buyerlisting.material.image','buyerlisting.images'])->where("status","!=","9")->where("created_source",$data['created_source'])->where("created_by",$data['created_by']);
             $offers = $offers->where("offer_id",$offer_id)->orderBy("created_at","DESC")->get();
             if(count($offers)>0){ $res_data['offer'] = $offers[0]; $message = "Offer Loaded Successully !"; }
         }
         if(count($offers)==0){ $code = 204; $message = "No Record Found !"; }
 
         return respondWithSuccess($res_data, 'BUYER', $request_log_id, $message, $code);
     }

     public function getBuyerChatList(Request $request){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);
        $code = 200; $message = "Chat Loaded Successully !";

        $buyer_id = $data['created_by'];

        $res_data = array();
        $chats = array();

        $seller_chat = SellerListingApplicant::where("buyer_id",$buyer_id)->where("status","!=","9")
        ->whereHas("comments",function($q){ $q->whereNotNull('comment_id'); })->orderBy("sell_list_app_id","DESC")->get()->toArray();

        $buyer_chat = BuyerListingApplicant::where("status","!=","9")
        ->whereHas("buyerlist",function($q) use($buyer_id){ $q->where('buyer_id',$buyer_id); })
        ->whereHas("comments",function($q){ $q->whereNotNull('comment_id'); })
        ->orderBy("buyer_list_app_id","DESC")->get()->toArray();

        $chats = array_merge($seller_chat,$buyer_chat);

        $res_data['chats'] = $chats;

        if(count($chats)==0){ $code = 204; $message = "No Chat Found !"; }

        return respondWithSuccess($res_data, 'BUYER', $request_log_id, $message, $code);
    }
}
