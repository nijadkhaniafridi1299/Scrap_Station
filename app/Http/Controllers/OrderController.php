<?php

namespace App\Http\Controllers;
use Hash;
use Session;
use App\Model\Seller;
use App\Model\User;
use App\Model\SystemStatus;
use App\Model\Image;
use App\Model\MaterialCategory;
use App\Model\SellerListing;
use App\Model\Address;
use App\Model\Offer;
use App\Model\Order;
use App\Model\OrderReview;
use App\Model\Driver;
use App\Model\Checkpoint;
use App\Model\OrderCheckpoint;
use App\Model\Group;
use App\Model\Role;
use App\Model\Notification;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;
use App\Message\Error;
use Validator;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{   
    // Web Methods
    public function getOrderDetailsweb(Request $request, $id = null){
        $data = $request->all();
        $users = User::where("status","!=","9")->get();
        $system_status = SystemStatus::where('key', 'LIKE', "%".'PROCESS'."%")->get();
        $orders = Order::with(["material","offer.seller","offer.buyer","driver","address", "systemstatus","review"]);
        if(!is_null($id)){
            $orders = Order::with(["material","review","offer","offer.seller","offer.buyer", "offer.sellerlisting","offer.buyerlisting","driver","address", "systemstatus"])->where("order_id",$id)->where("status",'!=',9)->orderBy("created_at","DESC")->first();
        //    return  $orders;
            return response()->json([
                'status'=>200,
                'orders'=> $orders,
            ]);
        }
        if (isset($data['mname']) && $data['mname'] != null && $data['mname'] != "") {
            $mname = $data['mname'];
            $orders->whereHas('material', function($q) use($mname){
                $q->where('name', 'LIKE', "%".$mname."%");
              });
          
        }
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $orders->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
        } 
        if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
            $orders->where('is_verified', $data['is_verify']);
        }
        if(isset($data['date']) && $data['date'] != null && $data['date'] != ""){
            $date= $data['date'];
            // dd($date);
            $orders->whereDate('created_at','=',$date);
        }  

             $orders =   $orders->orderBy("created_at","DESC")->get();
        //  dd($orders[1]->driver->fullname);
      
        return view('admin.order', compact('orders', 'data', 'system_status', 'users'));
    }
    public function chang_Orderlisting_statusweb(Request $request){
        $id = $request->input('id');

        $order=Order::find($id);
        $order->update([
            'is_verified' =>  $order->is_verified == 0 ? 1 : 0,
            'updated_source'=> 'user',
            'updated_at'=>  date("Y-m-d H:i:s"),
            'updated_by'=> Auth::user()->user_id,

        ]);
        
        return response()->json([
            'status'=>200,
             'order'=>$order,
        ]);
    }
    public function showtrackWeb($id=null){
        $track = OrderCheckpoint::with(["checkpoint"])->where('order_id', $id)->get();
        return response()->json([
            'status'=>200,
            'track'=>$track,
        ]);
    }
     
    //Mobile function
    public function getOrders(Request $request)
    {
        $errors = []; $message = "All Orders Fetch Successfully";
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();
        $by = $data["created_by"];
        $by_id = "buyer_id";
        
        if($data["created_source"]=="buyer"){ $by_id = "buyer_id"; }
        elseif($data["created_source"]=="seller"){ $by_id = "seller_id"; }

        $listing_source = "seller_listing";

        $orders_seller = Order::with(["address","driver","review","material.image","offer.buyer","offer.seller","offer.sellerlisting","offer.sellerlisting.images","images","payment"])->where("status","!=","9");
        if($data["created_source"] == "seller"){ $orders_seller = $orders_seller->where("is_verified","1"); }
        $orders_seller = $orders_seller->whereHas("offer",function($q) use ($by,$by_id,$listing_source){
            $q->where($by_id, '=', $by)->where("listing_source",$listing_source);
        });
        $orders_seller = $orders_seller->orderBy("status","ASC")->orderBy("created_at","DESC")->get()->toArray();


        ////////////////////////////
        $listing_source = "buyer_listing";
        $orders_buyer = Order::with(["address","driver","review","material.image","offer.buyer","offer.seller","offer.buyerlisting","offer.buyerlisting.images","images","payment"])->where("status","!=","9");
        if($data["created_source"] == "seller"){ $orders_buyer = $orders_buyer->where("is_verified","1"); }
        $orders_buyer = $orders_buyer->whereHas("offer",function($q) use ($by,$by_id,$listing_source){
            $q->where($by_id, '=', $by)->where("listing_source",$listing_source);
        });
        $orders_buyer = $orders_buyer->orderBy("status","ASC")->orderBy("created_at","DESC")->get()->toArray();
        ////////////////////////////

        $orders = array_merge($orders_seller, $orders_buyer);

        if(count($orders)==0){ $message = "No Order Present"; }
        $res_data['orders'] = $orders;
        $res_data['booked_quantity'] = Order::where('status', '2')
        ->whereHas("offer",function($q) use ($by,$by_id){
            $q->where($by_id, '=', $by);
        })->sum('quantity');
        $res_data['inprogress_quantity'] = Order::where('status', '4')
        ->whereHas("offer",function($q) use ($by,$by_id){
            $q->where($by_id, '=', $by);
        })->sum('quantity');
        $res_data['completed_quantity'] = Order::where('status', '10')
        ->whereHas("offer",function($q) use ($by,$by_id){
            $q->where($by_id, '=', $by);
        })->sum('quantity');
        //2, 4, 10

        if($data["created_source"]=="buyer"){
            $res_data['drivers'] = Driver::where("status","!=","9")->select(["driver_id","fullname","fullname_ar","email","mobile"])->get();
        }

        return respondWithSuccess($res_data, 'ORDER', $request_log_id, $message);
    }

    public function getSpecificOrder(Request $request,$order_id)
    {
        $errors = []; $message = "Order Loaded Successfully";
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['order_id'] = $order_id;

        $validator = Validator::make($data,[
            'order_id'  => 'required|int|min:1|exists:order,order_id'
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
 
         $listing_source = "seller_listing";

         $order = Order::with(["address","review","review.images","driver","checkpoints","material.image","offer.buyer","offer.seller","offer.sellerlisting","offer.sellerlisting.images","images","payment"])->where("status","!=","9")->where("order_id",$data['order_id']);
         if($data["created_source"] == "seller"){ $order = $order->where("is_verified","1"); }
         $order = $order->whereHas("offer",function($q) use ($by,$by_id,$listing_source){
            $q->where($by_id, '=', $by)->where("listing_source",$listing_source);
         });
         $order = $order->orderBy("status","ASC")->orderBy("created_at","DESC")->get();


         if(count($order)==0){
            $listing_source = "buyer_listing";

            $order = Order::with(["address","review","review.images","driver","checkpoints","material.image","offer.buyer","offer.seller","offer.buyerlisting","offer.buyerlisting.images","images","payment"])->where("status","!=","9")->where("order_id",$data['order_id']);
            if($data["created_source"] == "seller"){ $order = $order->where("is_verified","1"); }
            $order = $order->whereHas("offer",function($q) use ($by,$by_id,$listing_source){
               $q->where($by_id, '=', $by)->where("listing_source",$listing_source);
            });
            $order = $order->orderBy("status","ASC")->orderBy("created_at","DESC")->get();
         }

         if(count($order)==0){ $message = "No Order Present"; }
         else{ $order = $order[0]; }
         $res_data['order'] = $order;
         $res_data['drivers'] = Driver::where("status","!=","9")->select(["driver_id","fullname","fullname_ar","email","mobile"])->get();

        return respondWithSuccess($res_data, 'ORDER', $request_log_id, $message);
    }

    public function updateOrder(Request $request, $order_id){
        $errors = []; $code = 204;
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['order_id'] = $order_id;

        $validator = Validator::make($data,[
            'order_id'  => 'required|int|min:1|exists:order,order_id'
         ]);

         if($validator->fails()){
            array_push($errors, $validator->errors()->first());
            return respondWithError($errors,$request_log_id);
         }

         $order = Order::find($order_id);
         $seller = Seller::find($order->offer->seller_id);
         $seller_notification_id = $seller->fcm_token_for_seller_app;
         $seller_id = $seller->seller_id;
         $data['order']['updated_source'] = $data['created_source'];
         $data['order']['updated_by'] = $data['created_by'];
         $data['order']['updated_at'] = date("Y-m-d H:i:s");
         $data['order']['status'] = 4;
         $order = $order->change($data['order'],$order_id);

         if (!is_object($order)) { $errors = \App\Message\Error::get('order.change'); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

       // Notification Work Zeeshan Qureshi
       $get_Users = User::join('groups','users.group_id','=','groups.group_id')
        ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

       $notification_message = "Order # " .$order->order_no." is Updated.";

       if(count($get_Users)>0)
       {
           for($i=0; $i<count($get_Users); $i++)
           {
               $notification_id = $get_Users[$i]['fcm_token_for_web'];
               $source = 0;
               $is_sent = 0;
               if ($notification_id != "" || $notification_id != null) {
                   $title = "Order Update";
                   $type = "basic";
                   try {
                       $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                       $is_sent = 1;
                   } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
               }
               $insertNotification = Notification::insert([
                   'to_source' => "user",
                   'user_id' => $get_Users[$i]['user_id'],
                   'reference_type' => 'Order',
                   'reference_id' => $order->order_id,
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
            $title = "Order Update";
            $type = "basic";
            try {
                $res = send_notification_FCM($seller_notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        }
        $insertNotification = Notification::insert([
            'to_source' => "seller",
            'user_id' => $seller_id,
            'reference_type' => 'Order',
            'reference_id' => $order->order_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            'created_source' => $data['created_source'],
            'created_by' => $data['created_by'],
            'created_at' => date("Y-m-d H:i:s")
        ]);

        $res_data = array();
        $res_data['order'] = $order;
        return respondWithSuccess($res_data, 'ORDER', $request_log_id, "Order Updated Successfully", $code);
    }

    public function completeOrder(Request $request, $order_id){
        $errors = []; $code = 204;
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['order_id'] = $order_id;

        $validator = Validator::make($data,[
            'order_id'  => 'required|int|min:1|exists:order,order_id'
        ]);

        if($validator->fails()){
            array_push($errors, $validator->errors()->first());
            return respondWithError($errors,$request_log_id);
        }

        $order = Order::find($order_id);
        if($order->status == 10){
            return respondWithError(['Deal Already Completed'],$request_log_id); 
        }
        $seller = Seller::find($order->offer->seller_id);
        $seller_notification_id = $seller->fcm_token_for_seller_app;
        $seller_id = $seller->seller_id;

        $data['order']['updated_source'] = $data['created_source'];
        $data['order']['updated_by'] = $data['created_by'];
        $data['order']['updated_at'] = date("Y-m-d H:i:s");
        $data['order']['status'] = 10;
        $order = $order->change($data['order'], $order_id);

        if (!is_object($order)) { $errors = \App\Message\Error::get('order.change'); }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        if(isset($data['images']) && count($data['images'])>0){
            foreach($data['images'] as $img_path){
                $img_data = prepareImageData("order", $img_path, $data);
                $order->images()->create($img_data);
            }
        }
        $pay_no = "S-".generateNumber("Payment", "created_by", $data['created_by']);
        $pay_data = [
            "pay_no" => $pay_no,
            "pay_amount" => $order->offer->offered_price,
            "paid_amount" => $order->offer->offered_price,
            "received_by" => $order->offer->seller->seller_id,
            "created_source" => "user",
            "created_by" => "1",
            "created_at" => date("Y-m-d H:i:s")
        ];

        $order->payment()->create($pay_data);

        $order->offer->seller->wallet_amount += $order->offer->offered_price;
        if($order->offer->seller->deal_in_progress > 0) { $order->offer->seller->deal_in_progress -= 1; }
        $order->offer->seller->deal_completed += 1;
        $order->offer->seller->save();

        if($order->offer->buyer->deal_in_progress > 0) { $order->offer->buyer->deal_in_progress -= 1; }
        $order->offer->buyer->deal_completed += 1;
        $order->offer->buyer->save();

        // Notification Work Zeeshan Qureshi
        $get_Users = User::join('groups','users.group_id','=','groups.group_id')
        ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

        $notification_message = "Order # " .$order->order_no." is Completed.";

        if(count($get_Users)>0)
        {
            for($i=0; $i<count($get_Users); $i++)
            {
                $notification_id = $get_Users[$i]['fcm_token_for_web'];
                $source = 0;
                $is_sent = 0;
                if ($notification_id != "" || $notification_id != null) {
                    $title = "Order Update";
                    $type = "basic";
                    try {
                        $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                        $is_sent = 1;
                    } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                }
                $insertNotification = Notification::insert([
                    'to_source' => "user",
                    'user_id' => $get_Users[$i]['user_id'],
                    'reference_type' => 'Order',
                    'reference_id' => $order->order_id,
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
            $title = "Order Update";
            $type = "basic";
            try {
                $res = send_notification_FCM($seller_notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        }
        $insertNotification = Notification::insert([
            'to_source' => "seller",
            'user_id' => $seller_id,
            'reference_type' => 'Order',
            'reference_id' => $order->order_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            'created_source' => $data['created_source'],
            'created_by' => $data['created_by'],
            'created_at' => date("Y-m-d H:i:s")
        ]);

        return respondWithSuccess(null, 'ORDER', $request_log_id, "Order Completed Successfully", $code);
    }

    public function addOrderCheckpoint(Request $request, $order_id){
        $errors = []; $code = 201;
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['order_checkpoint']['order_id'] = $order_id;

         $order_checkpoint = new OrderCheckpoint();
         $validated_checkpoint = $order_checkpoint->validateAtStart($data['order_checkpoint']);

         if (!$validated_checkpoint ) {
            array_push($errors, \App\Message\Error::get('ordercheckpoint.start'));
        }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        $checkpoint_data = $data['order_checkpoint'];
        $checkpoint_data['created_source'] =  $data['created_source'];
        $checkpoint_data['created_by'] =  $data['created_by'];
        $checkpoint_data['created_at'] =  date('Y-m-d H:i:s');

        $order_checkpoint = $order_checkpoint->add($checkpoint_data);

         if (!is_object($order_checkpoint)) { $errors = \App\Message\Error::get('ordercheckpoint.add'); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        $res_data = array();
        $res_data['order_checkpoint'] = $order_checkpoint;
        return respondWithSuccess($res_data, 'ORDER', $request_log_id, "Checkpoint Added Successfully", $code);
    }

    public function addReview(Request $request, $order_id){
        $errors = []; $code = 201;
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['review']['order_id'] = $order_id;

        $data['review']['stars'] = (int)$data['review']['stars'];

         $review = new OrderReview();
         $validated_review = $review->validateAtStart($data['review']);

         if (!$validated_review ) {
            array_push($errors, \App\Message\Error::get('orderreview.start'));
        }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        $review_data = $data['review'];
        $review_data['created_source'] =  $data['created_source'];
        $review_data['created_by'] =  $data['created_by'];
        $review_data['created_at'] =  date('Y-m-d H:i:s');

        $review = $review->add($review_data);
         if (!is_object($review)) { $errors = \App\Message\Error::get('orderreview.add'); }
         if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

         if(isset($data['images']) && count($data['images'])>0){
            foreach($data['images'] as $img_path){
                $img_data = prepareImageData("review", $img_path, $data);
                $review->images()->create($img_data);
            }
        }

        $res_data = array();
        $res_data['review'] = $review;
        return respondWithSuccess($res_data, 'ORDER', $request_log_id, "Review Added Successfully", $code);
    }

}
