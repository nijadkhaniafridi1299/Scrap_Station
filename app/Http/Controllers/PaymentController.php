<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Model\Seller;
// use App\Model\Payment;
use App\Model\User;
use App\Model\SystemStatus;
use App\Models\Image;
use App\Model\Address;
use App\Model\Offer;
use App\Model\Payment;
use App\Message\Error;
use App\Model\Notification;
use Validator;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    // web function 
    public function showPaymentListWeb(Request $request, $id=null){
        $data = $request->all();
        // dd($id);
        $system_status = SystemStatus::where('key', 'LIKE', "%".'PROCESS'."%")->get();
       
        $payments = Payment::with(["order:order_id,price,order_no,offer_id,mat_id,driver_id,quantity,quantity_unit,total_price","order.driver","order.material:mat_id,sub_cat_id,product_code,name,name_ar,description","order.material.image","order.offer:offer_id,offer_no,seller_id,buyer_id,offered_price,listing_source,offered_price_with_vat,reason,status","order.offer.buyer","order.offer.seller","systemstatus"])
        // ->whereHas('order.systemstatus', function($q) {
        //     // $q->where('key', 'LIKE', "%".'PROCESS'."%");
        //     // dd(DB::getQueryLog());
        //     })
        ->where('ref_type', 'order')->where("status","!=","9");
     


        if(isset($data['fullname']) && $data['fullname'] != null && $data['fullname'] != ""){
            $fullname = $data['fullname'];
         
            $payments->whereHas('order.offer.seller', function($q) use($fullname){
            $q->where('fullname', 'LIKE', "%".$fullname."%");
            // dd(DB::getQueryLog());
            });
       
         
        }
        if(isset($data['order_no']) && $data['order_no'] != null && $data['order_no'] != ""){
          $order_no = $data['order_no'];
   
          $payments->whereHas('order', function($q) use( $order_no){
            $q->where('order_no',$order_no);
            });
          
        }
        if(isset($data['mname']) && $data['mname'] != null && $data['mname'] != ""){
            $mname= $data['mname'];
     
            $payments->whereHas('order.material', function($q) use($mname){
                $q->where('name', 'LIKE', "%". $mname."%");
              });
           
          }
          if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $payments->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
          }
          if(isset($data['date']) && $data['date'] != null && $data['date'] != ""){
            $date= $data['date'];
            // dd($date);
            $payments->whereDate('created_at','=',$date);
          } 
        if(!is_null($id)){
           
            $payments->whereHas("order.offer.seller",function($q) use ($id){
                // $q->where('seller_id', $id);
            })->where('pay_id', $id);
             
            $payments = $payments->orderBy("status","ASC")->orderBy("created_at","DESC")->first();
        
            return response()->json([
                'code'=>20,
                'payment'=> $payments
            ]);
        }
        if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
            $payments->where('is_verified', $data['is_verify']);
          } 
         
        $payments = $payments->orderBy("status","ASC")->orderBy("created_at","DESC")->get();
       
         return view('admin.payment', compact('payments', 'system_status', 'data'));
  
    }
    public function chang_payment_statusweb(Request $request){
        $id = $request->input('id');

        $payment=Payment::find($id);
        $payment->update([
            'is_verified' => $payment->is_verified == 0 ? 1 : 0,
            'updated_source'=> 'user',
            'updated_at'=>  date("Y-m-d H:i:s"),
            'updated_by'=> Auth::user()->user_id,

        ]);
        
        return response()->json([
            'status'=>200,
             'payment'=>$payment,
        ]);
    }

    // mobile functions
    public function getPayments(Request $request)
    {
        $errors = []; $message = "All Payment Details Fetch Successfully";
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();
        $by = $data["created_by"];
        $by_id = "buyer_id";
        
        if($data["created_source"]=="buyer"){ $by_id = "buyer_id"; }
        elseif($data["created_source"]=="seller"){ $by_id = "seller_id"; }

        $listing_source = "seller_listing";
        $payments_seller = Payment::whereHas("order.offer",function($q) use ($by,$by_id,$listing_source){
            $q->where($by_id, '=', $by)->where("listing_source",$listing_source);
        })->with(["order:order_id,order_no,offer_id,mat_id,driver_id,quantity,quantity_unit,price,total_price","order.driver","order.material:mat_id,sub_cat_id,product_code,name,name_ar,description","order.material.image","order.offer:offer_id,offer_no,listing_id,seller_id,buyer_id,offered_price,offered_price_with_vat,reason,status","order.offer.buyer","order.offer.seller","order.offer.sellerlisting"])
        ->where("ref_type","order")
        ->where("status","!=","9")->orderBy("status","ASC")->orderBy("created_at","DESC")->get()->toArray();

        $listing_source = "buyer_listing";
        $payments_buyer = Payment::whereHas("order.offer",function($q) use ($by,$by_id,$listing_source){
            $q->where($by_id, '=', $by)->where("listing_source",$listing_source);
        })->with(["order:order_id,order_no,offer_id,mat_id,driver_id,quantity,quantity_unit,price,total_price","order.driver","order.material:mat_id,sub_cat_id,product_code,name,name_ar,description","order.material.image","order.offer:offer_id,offer_no,listing_id,seller_id,buyer_id,offered_price,offered_price_with_vat,reason,status","order.offer.buyer","order.offer.seller","order.offer.buyerlisting"])
        ->where("ref_type","order")
        ->where("status","!=","9")->orderBy("status","ASC")->orderBy("created_at","DESC")->get()->toArray();

        $payments = array_merge($payments_seller, $payments_buyer);

        if(count($payments)==0){ $message = "No Payment Present"; }
        $res_data['payments'] = $payments;

        return respondWithSuccess($res_data, 'PAYMENT', $request_log_id, $message);
    }

    public function getSpecificPayment(Request $request,$pay_id)
    {
        $errors = []; $message = "Payment Loaded Successfully";
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['pay_id'] = $pay_id;

        $validator = Validator::make($data,[
            'pay_id'  => 'required|int|min:1|exists:payment,pay_id'
         ]);

         if($validator->fails()){
            array_push($errors, $validator->errors()->first());
            return respondWithError($errors,$request_log_id);
         }

         $res_data = array();
         $by = $data["created_by"];
         $by_id = "buyer_id";
         
         if($data["created_source"]=="buyer"){ $by_id = "buyer_id"; }
         elseif($data["created_source"]=="seller"){ $by_id = "seller_id"; }
 
         $payment = Payment::with(["order:order_id,order_no,offer_id,mat_id,driver_id,quantity,quantity_unit,price,total_price","order.driver","order.material:mat_id,sub_cat_id,product_code,name,name_ar,description","order.material.image","order.offer:offer_id,offer_no,listing_id,seller_id,buyer_id,offered_price,offered_price_with_vat,reason,status","order.offer.buyer","order.offer.seller","order.offer.sellerlisting"])->where("status","!=","9")->where("pay_id",$data['pay_id'])
         ->whereHas("order.offer",function($q) use ($by,$by_id){
             $q->where($by_id, '=', $by);
         });
         $payment = $payment->orderBy("status","ASC")->orderBy("created_at","DESC")->get();
         if(count($payment)==0){ $message = "No Payment Present"; }
         $res_data['payment'] = $payment[0];

        return respondWithSuccess($res_data, 'PAYMENT', $request_log_id, $message);
    }

    public function completePayment(Request $request, $pay_id)
    {
        $errors = []; $message = "Payment Completed Successfully"; $code = 204;
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['pay_id'] = $pay_id;

        $validator = Validator::make($data,[
            'pay_id'  => 'required|int|min:1|exists:payment,pay_id'
         ]);

        if($validator->fails()){
            array_push($errors, $validator->errors()->first());
            return respondWithError($errors,$request_log_id);
        }

        $pay_data = [
            "received_by"=>$data['payment']['received_by'],
            "paid_amount"=>$data['payment']['paid_amount'],
            "status"=>10,
            "updated_source"=>$data['created_source'],
            "updated_by"=>$data['created_by'],
            "updated_at"=>date("Y-m-d H:i:s")
        ];
        $payment = Payment::find($pay_id);

        $seller = Seller::find($payment->order->offer->seller_id);
        $seller_notification_id = $seller->fcm_token_for_seller_app;
        $seller_id = $seller->seller_id;
        $payment = $payment->change($pay_data, $pay_id);

        if (!is_object($payment)) { $errors = \App\Message\Error::get('payment.change'); }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
        
        // Notification Work Zeeshan Qureshi
        $get_Users = User::join('groups','users.group_id','=','groups.group_id')
        ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

        $notification_message = "Payment # " .$payment->pay_no." is Completed.";

        if(count($get_Users)>0)
        {
            for($i=0; $i<count($get_Users); $i++)
            {
                $notification_id = $get_Users[$i]['fcm_token_for_web'];
                $source = 0;
                $is_sent = 0;
                if ($notification_id != "" || $notification_id != null) {
                    $title = "Payment Update";
                    $type = "basic";
                    try {
                        $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                        $is_sent = 1;
                    } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                }
                $insertNotification = Notification::insert([
                    'to_source' => "user",
                    'user_id' => $get_Users[$i]['user_id'],
                    'reference_type' => 'Payment',
                    'reference_id' => $payment->pay_id,
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
            $title = "Payment Update";
            $type = "basic";
            try {
                $res = send_notification_FCM($seller_notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        }
        $insertNotification = Notification::insert([
            'to_source' => "seller",
            'user_id' => $seller_id,
            'reference_type' => 'Payment',
            'reference_id' => $payment->pay_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            'created_source' => $data['created_source'],
            'created_by' => $data['created_by'],
            'created_at' => date("Y-m-d H:i:s")
        ]);

        return respondWithSuccess(null, 'PAYMENT', $request_log_id, $message, $code);
    }

    public function verifyPayment(Request $request, $pay_id)
    {
        $errors = []; $message = "Payment Verified Successfully"; $code = 204;
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['pay_id'] = $pay_id;

        $validator = Validator::make($data,[
            'pay_id'  => 'required|int|min:1|exists:payment,pay_id'
         ]);

        if($validator->fails()){
            array_push($errors, $validator->errors()->first());
            return respondWithError($errors,$request_log_id);
        }
        $pay_data = [
            "is_verified" => 1,
            "status" => 10,
            "updated_source" => $data['created_source'],
            "updated_by" => $data['created_by'],
            "updated_at" => date("Y-m-d H:i:s")
        ];
        $payment = Payment::find($pay_id);
        $payment = $payment->change($pay_data, $pay_id);

        if (!is_object($payment)) { $errors = \App\Message\Error::get('payment.change'); }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        return respondWithSuccess(null, 'PAYMENT', $request_log_id, $message, $code);
    }
}