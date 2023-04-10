<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\Seller;
use App\Model\Buyer;
use App\Model\User;

use App\Model\Image;
use App\Model\SellerListingApplicant;
use App\Model\MaterialCategory;
use App\Model\SellerListing;
use App\Model\BuyerListing;
use App\Model\BuyerListingApplicant;
use App\Model\Address;
use App\Model\Offer;
use App\Model\Order;
use App\Model\Checkpoint;
use App\Model\Notification;
use App\Http\Controllers\OptionController;
use App\Model\Payment;
use App\Model\SystemStatus;
use App\Message\Error;
use App\Model\MaterialSubCategory;
use App\Model\Group;
use App\Model\Role;
use Illuminate\Support\Facades\Auth;
use DB;
use Validator;
use Hash;
use Session;

class SellerController extends Controller
{
  

    // Mobile Controller Functions
    public function create(Request $request)
    {
        $errors = [];
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);
        $seller = new Seller();

        //step1: create a new seller in sellers
        $data['seller']['created_at'] =  date('Y-m-d H:i:s');

        if(isset($data['created_by'])){ $data['seller']['created_by'] = $data['created_by']; }
        if(isset($data['created_source'])){ $data['seller']['created_source'] = $data['created_source']; }

        $data['seller'] = sanitizeData($data['seller']);

        if(!empty($data['seller']['mobile']) && is_numeric($data['seller']['mobile'])){
            $data['seller']['mobile'] = filterMobileNumber($data['seller']['mobile']);
        }

        $seller = new Seller();
        $validated_seller = $seller->validateAtStart($data['seller']);

        if (!$validated_seller ) {
            array_push($errors, \App\Message\Error::get('seller.start'));
        }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        $seller = $seller->add($data['seller']);

        if (!is_object($seller)) {
            $errors = \App\Message\Error::get('seller.add');
        }

        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        $getSuppName=$data['seller']['fullname'];

        $get_Users=User::join('groups','users.group_id','=','groups.group_id')
            ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

        if(count($get_Users)>0)
        {
        for($i=0;$i<count($get_Users);$i++)
        {
        $notification_id=$get_Users[$i]['fcm_token_for_web'];
        $source = 0;
        $is_sent = 0;
        $notification_message = "Seller " .$getSuppName." wants to register" ;

        if ($notification_id != "" || $notification_id != null) {
            $title = "Seller Registration";
            $type = "basic";
            try {
                $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        } 
        $insertNotification=Notification::insert([
            'to_source' => "user",
            'user_id'=>$get_Users[$i]['user_id'],
            'reference_type' => 'seller',
            'reference_id' => $seller->seller_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            // 'created_source' => $data['created_source'],
            // 'created_by' => $data['created_by'],
        ]);
        }
        }

        return respondWithSuccess($seller, 'SELLER', $request_log_id, "Your registration request sent to Scrap Station for Approval.", 201);
    }

    public function updateSeller(Request $request, $seller_id)
    {
        $errors = [];
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['seller_id'] = $seller_id;

        $validator = Validator::make($data,[
            'seller_id'  => 'required|int|min:1|exists:seller,seller_id',
        ]);

        if($validator->fails()){
            array_push($errors, $validator->errors()->first()); return respondWithError($errors,$request_log_id);
        }

        $seller = Seller::find($seller_id);
        $data['seller']['updated_at'] =  date('Y-m-d H:i:s');
        if(isset($data['created_by'])){ $data['seller']['updated_by'] = $data['created_by']; }
        if(isset($data['created_source'])){ $data['seller']['updated_source'] = $data['created_source']; }

        $seller = $seller->change($data['seller'],$seller_id);
        if (!is_object($seller)) { $errors = \App\Message\Error::get('seller.change'); }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        if(isset($data['images']) && count($data['images'])>0){
            foreach($data['images'] as $img_path){
                $img_data = prepareImageData("seller_iqama",$img_path,$data,"Seller Iqama Information");
                $seller->image()->create($img_data);
            }
        }

        $getSuppName=$data['seller']['fullname'];

        $get_Users=User::join('groups','users.group_id','=','groups.group_id')
            ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

        if(count($get_Users)>0)
        {
        for($i=0;$i<count($get_Users);$i++)
        {
            $notification_id=$get_Users[$i]['fcm_token_for_web'];
            $source = 0;
            $is_sent = 0;
            $notification_message = "Seller " .$getSuppName." Updated Information" ;

            if ($notification_id != "" || $notification_id != null) {
                $title = "seller Registration";
                $type = "basic";
                try {
                    $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                    $is_sent = 1;
                } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
            } 
            $insertNotification=Notification::insert([
                'to_source' => "user",
                'user_id'=>$get_Users[$i]['user_id'],
                'reference_type' => 'seller',
                'reference_id' => $seller->seller_id,
                'notification_body' => $notification_message,
                'is_sent' => $is_sent,
                'created_source' => $data->created_source,
                'created_by' => $data->created_by,
            ]);
            }
        }

        return respondWithSuccess($seller, 'SELLER', $request_log_id, "Your Information is submitted to update in Scrap Station.", 204);
    }

    public function getDashboardDetails(Request $request){
        $errors = [];
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();
        $seller_id = $data['seller_id'];

        $seller = Seller::find($seller_id);

        if (!is_object($seller)) {
            Error::trigger("seller.dashboard", ["Seller does not exist."]);
            array_push($errors, \App\Message\Error::get('seller.dashboard'));
        }

        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        $res_data = array();
        $seller_data = array();

        $seller_data['seller_id'] = $seller_id;
        $seller_data['seller_name'] = $seller->fullname;
        $seller_data['email'] = $seller->email;
        $seller_data['mobile'] = $seller->mobile;
        $seller_data['registration_date'] = $seller->registration_date;
        $seller_data['iqama_cr_no'] = $seller->iqama_cr_no;
        $seller_data['refferal_code'] = $seller->refferal_code;
        $seller_data['wallet_amount'] = $seller->wallet_amount;
        $seller_data['deal_in_progress'] = $seller->deal_in_progress;
        $seller_data['deal_completed'] = $seller->deal_completed;
        $seller_data['listing_count'] = count($seller->listings);
        // $seller_data['iqama_cr_file'] = $seller->iqama_cr_file;

        $address = null;
        
        if($seller->addressdetail != null){
          $address = Seller::with('addressdetail')->where('seller_id',$seller_id)->get();
          $address =  $address[0]->addressdetail;
        }

        $seller_data['location'] = null;
        if(!empty($address->latitude)){
            $seller_data['location']['lat'] = $address->latitude;
            $seller_data['location']['lan'] = $address->longitude;
            $seller_data['location']['address'] = $address->address;
        }

        $res_data["seller"] = $seller_data;

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

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, "");
    }

    public function createListing(Request $request){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();

        $data['seller_listing'] = sanitizeData($data['seller_listing']);
        $seller_listing_data = $data['seller_listing'];
        $seller_listing_data['seller_id'] = $data['created_by'];
        $seller_listing_data['created_source'] = $data['created_source'];
        $seller_listing_data['created_by'] = $data['created_by'];
        $seller_listing_data['created_at'] = date("Y-m-d H:i:s");
        $seller_listing_data['status'] = 7;

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

        if($address_id != null){ $seller_listing_data['address_id'] = $address_id; }

        $listing_no = generateNumber("SellerListing", "seller_id", $data['created_by']);

        $seller_listing_data['listing_no'] = $listing_no;

        $seller_listing = new SellerListing();
        $validated_seller_listing = $seller_listing->validateAtStart($seller_listing_data);

        if (!$validated_seller_listing ) { array_push($errors, \App\Message\Error::get('sellerlisting.start')); }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        $seller_listing = $seller_listing->add($seller_listing_data);

        if (!is_object($seller_listing)) { $errors = \App\Message\Error::get('sellerlisting.add'); }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        if(isset($data['images']) && count($data['images'])>0){
            foreach($data['images'] as $img_path){
                $img_data = prepareImageData("seller_listing",$img_path,$data);
                $seller_listing->images()->create($img_data);
            }
        }
        $res_data['seller_listing'] = $seller_listing;

        $subcat_obj = $seller_listing->material->subcategory;
        $subcat_obj->current_avl_quantity += $seller_listing->quantity;
        $subcat_obj->save();

        $get_Users = User::join('groups','users.group_id','=','groups.group_id')
            ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

        $notification_message = "Request for Seller Listing " .$seller_listing->listing_no." is generated. Please check and verify." ;

        if(count($get_Users)>0)
        {
            for($i=0; $i<count($get_Users); $i++)
            {
                $notification_id = $get_Users[$i]['fcm_token_for_web'];
                $source = 0;
                $is_sent = 0;
                if ($notification_id != "" || $notification_id != null) {
                    $title = "Seller Listing Request";
                    $type = "basic";
                    try {
                        $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                        $is_sent = 1;
                    } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                }
                $insertNotification = Notification::insert([
                    'to_source' => "user",
                    'user_id' => $get_Users[$i]['user_id'],
                    'reference_type' => 'Seller Listing',
                    'reference_id' => $seller_listing->sell_list_id,
                    'notification_body' => $notification_message,
                    'is_sent' => $is_sent,
                    'created_source' => $data['created_source'],
                    'created_by' => $data['created_by'],
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
        }

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, "Thank you for using service. You will be contacted from CSR for verfication of this listing.",201);
    }

    public function showListing(Request $request,$sell_list_id=null){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();
        $listings = null; $code = 200; $message = "List Loaded Successfully!";

        if(!is_null($sell_list_id)){
            $listings = SellerListing::with(['images','address','material','material.image','applicants','applicants.offers'])->where("created_source",$data['created_source'])->where("created_by",$data['created_by'])->where("sell_list_id",$sell_list_id)->where("status",'!=',9)->orderBy("created_at","DESC")->get();
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
            $listings = SellerListing::with(['images','address','material','material.image','applicants','applicants.offers'])->where("created_source",$data['created_source'])->where("created_by",$data['created_by'])->where("status",'!=',9)->orderBy("created_at","DESC")->get();
            $res_data['listings'] = $listings;
        }
        
        if(count($listings)==0){ $code = 204; $message = "No Record Found!"; }

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, $message, $code);
    }

    public function getListingOffers(Request $request,$sell_list_id){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();
        $code = 200; $message = "Offers Loaded Successfully !";

        $offers = Offer::with(['sellerlisting','buyer'])->where("status","!=","9")->where("listing_source","seller_listing")->where("listing_id",$sell_list_id)->where("seller_id",$data['created_by'])->orderBy("created_at","DESC")->get();
        $res_data['offers'] = $offers;

        if(count($offers)==0){ $code = 204; $message = "No Offers Found Against This Listing !"; }

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, $message, $code);
    }

    public function getLatestOffers(Request $request,$sell_list_id = null){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();
        $code = 200; $message = "Offers Loaded Successfully !";

        $offer_arr = Offer::where("status","!=","9")->where("listing_source","seller_listing")->where("seller_id",$data['created_by']);
        if(!is_null($sell_list_id)){ $offer_arr = $offer_arr->where("listing_id",$sell_list_id); }
        $offer_arr = $offer_arr->groupBy("application_source")->groupBy("application_id")->orderBy("created_at","DESC")->pluck('application_id')->toArray();

        $offers = array();

        foreach($offer_arr as $app_id){
            $temp_offer = Offer::with(['sellerlisting','buyer','seller'])->where("status","!=","9")->where("listing_source","seller_listing")->where("seller_id",$data['created_by']);
            $temp_offer = $temp_offer->where("application_id",$app_id)->orderBy("created_at","DESC")->orderBy("listing_id","DESC")->get();
            array_push($offers, $temp_offer[0]);
        }
        // dd($offers);

        if(count($offers)==0){ $code = 204; $message = "No Offers Available!"; }

        $res_data['offers'] = $offers;

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, $message, $code);
    }

    public function getLatestBuyerListOffers(Request $request,$buyer_list_id = null){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();
        $code = 200; $message = "Offers Loaded Successfully !";

        $offer_arr = Offer::where("status","!=","9")->where("listing_source","buyer_listing")->where("seller_id",$data['created_by']);
        if(!is_null($buyer_list_id)){ $offer_arr = $offer_arr->where("listing_id",$buyer_list_id); }
        $offer_arr = $offer_arr->groupBy("application_source")->groupBy("application_id")->orderBy("created_at","DESC")->pluck('application_id')->toArray();

        $offers = array();

        foreach($offer_arr as $app_id){
            $temp_offer = Offer::with(['buyerlisting','buyer','seller'])->where("status","!=","9")->where("listing_source","buyer_listing")->where("seller_id",$data['created_by']);
            $temp_offer = $temp_offer->where("application_id",$app_id)->orderBy("created_at","DESC")->orderBy("listing_id","DESC")->get();
            array_push($offers, $temp_offer[0]);
        }
        // dd($offers);

        if(count($offers)==0){ $code = 204; $message = "No Offers Available!"; }

        $res_data['offers'] = $offers;

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, $message, $code);
    }

    public function getOffers(Request $request, $offer_id = null){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);
        $code = 200; $message = "Offers Loaded Successully !";

        $res_data = array();

        $offers = Offer::with(['buyer','seller','sellerlisting','sellerlisting.address','sellerlisting.material','sellerlisting.material.image','sellerlisting.images'])->where("status","!=","9")->where("seller_id",$data['created_by']);
        if(!is_null($offer_id)){
            $offers = $offers->where("offer_id",$offer_id)->orderBy("created_at","DESC")->get();
            if(count($offers)>0){ $res_data['offer'] = $offers[0]; $message = "Offer Loaded Successully !"; }
        }
        else{
            $offers = $offers->orderBy("created_at","DESC")->get();
            $res_data['offers'] = $offers;
        }
        if(count($offers)==0){ $code = 204; $message = "No Offer Found !"; }

        return respondWithSuccess($res_data, 'Seller', $request_log_id, $message, $code);
    }

    public function updateOffer(Request $request){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);
        $code = 204; $message = "Offer Updated Successully !";

        $validator = Validator::make($data,[
            'offer_id'  => 'required|int|min:1|exists:offer,offer_id',
            'status'  => 'required|int'
         ]);
         if($validator->fails()){
            array_push($errors, $validator->errors()->first());
            return respondWithError($errors,$request_log_id);
         }

        $buyer_notification_id = null;
        $buyer_id = null;

        $offer = Offer::find($data['offer_id']);
        $offer_no = $offer->offer_no;
        $offer_id = $offer->offer_id;

        $notification_message = "Offer # " .$offer_no." is updated";
        $title = "Offer Update"; $reference_type = "Offer"; $reference_id = $offer_id;

        if($data['status']=="10"){
            $order = new Order();
            $order_data['offer_id'] = $offer->offer_id;
            $order_data['mat_id'] = $offer->listing->mat_id;
            $order_data['quantity'] = $offer->listing->quantity;
            $order_data['quantity_unit'] = $offer->listing->quantity_unit;
            $order_data['address_id'] = $offer->listing->address_id;
            $order_data['price'] = $offer->offered_price;
            $order_data['total_price'] = $offer->offered_price_with_vat;
            $order_data['created_source'] = $data['created_source'];
            $order_data['created_by'] = $data['created_by'];
            $order_data['created_at'] = date("Y-m-d H:i:s");
            $validated_order = $order->validateAtStart($order_data);
            if (!$validated_order ) {
                array_push($errors, \App\Message\Error::get('order.start'));
                if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
            }
            $order = $order->add($order_data);
            if (!is_object($order)) { $errors = \App\Message\Error::get('order.add'); }
            if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
            $message = "Order Request Generated Successfully. Our Representative will contact you for confirmation !";
            $notification_message = "Order # " .$order->order_no." is Generated";
            $title = "Order Created"; $reference_type = "Order"; $reference_id = $order->order_id;

            $offer->listing->status = 8;
            $offer->listing->closed_reason = "Order Generated";
            $offer->listing->save();

            foreach($offer->listing->openoffers as $other_ofr){ // update other offers
                if($offer->offer_id != $other_ofr->offer_id){
                    $other_ofr_obj = Offer::find($other_ofr->offer_id);
                    $other_ofr_obj->status = 5; $other_ofr_obj->save();
                }
            }

            $slr = Seller::find($offer->seller_id);
            $slr->deal_in_progress += 1;
            $slr->save();

            $byr = Buyer::find($offer->buyer_id);
            $buyer_notification_id = $byr->fcm_token_for_buyer_app;
            $buyer_id = $offer->buyer_id;
            $byr->deal_in_progress += 1;
            $byr->save();
        }
        else{
            $byr = Buyer::find($offer->buyer_id);
            $buyer_notification_id = $byr->fcm_token_for_buyer_app;
            $buyer_id = $offer->buyer_id;
        }
        
        $offer->status = $data['status'];
        $offer->reason = $data['reason'];
        $offer->updated_source = $data['updated_source'];
        $offer->updated_at = date("Y-m-d H:i:s");
        $offer->updated_by = $data['created_by'];
        $offer = $offer->save();

        //Notification Code // Zeeshan Qureshi Code
        $get_Users = User::join('groups','users.group_id','=','groups.group_id')
        ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

        if(count($get_Users)>0)
        {
            for($i=0; $i<count($get_Users); $i++)
            {
                $notification_id = $get_Users[$i]['fcm_token_for_web'];
                $source = 0;
                $is_sent = 0;
                
                if ($notification_id != "" || $notification_id != null) {
                    $type = "basic";
                    try {
                        $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                        $is_sent = 1;
                    } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                }
                $insertNotification = Notification::insert([
                    'to_source' => "user",
                    'user_id' => $get_Users[$i]['user_id'],
                    'reference_type' => $reference_type,
                    'reference_id' => $reference_id,
                    'notification_body' => $notification_message,
                    'is_sent' => $is_sent,
                    'created_source' => $data['created_source'],
                    'created_by' => $data['created_by'],
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
        }

        // notification for buyer
        $source = 1;
        $is_sent = 0;
        if ($buyer_notification_id != "" || $buyer_notification_id != null) {
            $type = "basic";
            try {
                $res = send_notification_FCM($buyer_notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        }
        $insertNotification = Notification::insert([
            'to_source' => "buyer",
            'user_id' => $buyer_id,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            'created_source' => $data['created_source'],
            'created_by' => $data['created_by'],
            'created_at' => date("Y-m-d H:i:s")
        ]);

        return respondWithSuccess(null, 'SELLER', $request_log_id, $message, $code);
    }

    public function closeListing(Request $request, $sell_list_id){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);
        $code = 204; $message = "Listing is closed Now !";

        $data['sell_list_id'] = $sell_list_id;

        $validator = Validator::make($data,[
            'sell_list_id'  => 'required|int|min:1|exists:seller_listing,sell_list_id',
            'closed_reason'  => 'required'
         ]);
         if($validator->fails()){
            array_push($errors, $validator->errors()->first());
            return respondWithError($errors,$request_log_id);
         }

        $seller_listing = SellerListing::find($sell_list_id);
        $seller_listing->status = 8;
        $seller_listing->closed_reason = $data['closed_reason'];
        $seller_listing->save();

        // price deduction and refund


        //Notification Code // Zeeshan Qureshi Code
        $get_Users = User::join('groups','users.group_id','=','groups.group_id')
        ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();

        if(count($get_Users)>0)
        {
            for($i=0; $i<count($get_Users); $i++)
            {
                $notification_id = $get_Users[$i]['fcm_token_for_web'];
                $source = 0;
                $is_sent = 0;
                $notification_message = "Seller Listing # " .$seller_listing->listing_no." is closed with reason ".$data['closed_reason'];
                if ($notification_id != "" || $notification_id != null) {
                    $title = "Seller Listing";
                    $type = "basic";
                    try {
                        $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                        $is_sent = 1;
                    } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                }
                $insertNotification = Notification::insert([
                    'to_source' => "user",
                    'user_id' => $get_Users[$i]['user_id'],
                    'reference_type' => 'Seller Listing',
                    'reference_id' => $seller_listing->sell_list_id,
                    'notification_body' => $notification_message,
                    'is_sent' => $is_sent,
                    'created_source' => $data['created_source'],
                    'created_by' => $data['created_by'],
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
        }

        // remove all offers of closed listing
        $seller_listing->offers()->update(['status' => 9]);

        return respondWithSuccess(null, 'Seller', $request_log_id, $message, $code);
    }

    public function getMaterialsOfSubcategory(Request $request,$cat_id){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);
        $code = 200; $message = "List Loaded Successfully!";

        $res_data = array();

        $buyer_listings = BuyerListing::with(['images','address','material','material.image','buyer'])->where("status","7")->where("is_verified",1);
        $buyer_listings = $buyer_listings->whereHas('material.subcategory.category', function($query) use ($cat_id){
            $query->where('cat_id', $cat_id);
        });
        $buyer_listings = $buyer_listings->orderBy("created_at","DESC")->get();

        foreach($buyer_listings as &$b_listing){
            $b_listing['my_applicant_id'] = null;
            $b_list_app = BuyerListingApplicant::where("seller_id",$data['created_by'])->where("buyer_list_id",$b_listing->buyer_list_id)->select("buyer_list_app_id")->get();
            if(count($b_list_app)>0){
                $b_listing['my_applicant_id'] = $b_list_app[0]['buyer_list_app_id'];
            }
        }
        $res_data['buyer_listings'] = $buyer_listings;

        if(count($buyer_listings)==0){ $code = 204; $message = "No Listings Found!"; }

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, $message, $code);
    }

    public function getBuyerCategoriesInformation(Request $request){
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
                            if($mat->verifiedbuyerlistings != null){ $list_count += count($mat->verifiedbuyerlistings); }
                        }
                    }
                }
            }
            
            $material_categories[$cat_ky]['listings'] = $list_count;
        }

        $res_data["material_categories"] = $material_categories;
        return respondWithSuccess($res_data, 'SELLER', $request_log_id, "");
    }

    public function applyOnBuyerLising(Request $request, $buyer_list_id)
    {
        $errors = []; $message = "Successfully applied on Buyer Listing"; $code = 204;
        $data = $request->json()->all();//$request->all();

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $data['buyer_list_id'] = $buyer_list_id;

        $validator = Validator::make($data,[
            'buyer_list_id'  => 'required|int|min:1|exists:buyer_listing,buyer_list_id'
         ]);

        if($validator->fails()){
            array_push($errors, $validator->errors()->first());
            return respondWithError($errors,$request_log_id);
        }

        $res_data = array();

        $buyer_applicant = "";

        $buyer_applicant = BuyerListingApplicant::where("buyer_list_id",$buyer_list_id)->where("seller_id",$data['created_by'])->get();
        if(count($buyer_applicant)>0){ $buyer_applicant = $buyer_applicant[0]; }
        else{
            $buy_list_app_no = generateNumber("BuyerListingApplicant", "seller_id", $data['created_by']);
            $buy_app_data = [
                "buyer_list_id" => $buyer_list_id,
                "seller_id" => $data['created_by'],
                "buy_list_app_no" => $buy_list_app_no,
                "created_source" => $data['created_source'],
                "created_by" => $data['created_by'],
                "created_at" => date("Y-m-d H:i:s")
            ];
    
            $buyer_applicant = new BuyerListingApplicant();
            $buyer_applicant = $buyer_applicant->add($buy_app_data);
    
            if (!is_object($buyer_applicant)) { $errors = \App\Message\Error::get('buyerlistingapplicant.add'); }
            if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
        }

        $buyer_id = $buyer_applicant->buyerlist->buyer_id;
        $buyer_obj = Buyer::find($buyer_id);
        $buyer_notification_id = $buyer_obj->fcm_token_for_buyer_app;

        //Notification Code // Zeeshan Qureshi Code
        $get_Users = User::join('groups','users.group_id','=','groups.group_id')
        ->where('groups.role_key','ADMIN')->get(['fcm_token_for_web','user_id'])->toArray();
        
        $notification_message = "Seller Applied on Buyer Listing # ".$buyer_applicant->buyerlist->listing_no;

        if(count($get_Users)>0)
        {
            for($i=0; $i<count($get_Users); $i++)
            {
                $notification_id = $get_Users[$i]['fcm_token_for_web'];
                $source = 0;
                $is_sent = 0;
                if ($notification_id != "" || $notification_id != null) {
                    $title = "Buyer Listing Applied";
                    $type = "basic";
                    try {
                        $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                        $is_sent = 1;
                    } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
                }
                $insertNotification = Notification::insert([
                    'to_source' => "user",
                    'user_id' => $get_Users[$i]['user_id'],
                    'reference_type' => 'Buyer Listing Applicant',
                    'reference_id' => $buyer_applicant->buyer_list_app_id,
                    'notification_body' => $notification_message,
                    'is_sent' => $is_sent,
                    'created_source' => $data['created_source'],
                    'created_by' => $data['created_by'],
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
        }

        // notification for buyer
        $source = 1;
        $is_sent = 0;
        if ($buyer_notification_id != "" || $buyer_notification_id != null) {
            $title = "Buyer Listing Applied";
            $type = "basic";
            try {
                $res = send_notification_FCM($buyer_notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        }
        $insertNotification = Notification::insert([
            'to_source' => "buyer",
            'user_id' => $buyer_id,
            'reference_type' => 'Buyer Listing Applicant',
            'reference_id' => $buyer_applicant->buyer_list_app_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            'created_source' => $data['created_source'],
            'created_by' => $data['created_by'],
            'created_at' => date("Y-m-d H:i:s")
        ]);

        $res_data['buyer_applicant'] = $buyer_applicant;

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, $message, $code);
    }

    public function getSellerChatList(Request $request){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);
        $code = 200; $message = "Chat Loaded Successully !";

        $seller_id = $data['created_by'];

        $res_data = array();
        $chats = array();

        $buyer_chat = BuyerListingApplicant::where("seller_id",$seller_id)->where("status","!=","9")
        ->whereHas("comments",function($q){ $q->whereNotNull('comment_id'); })->orderBy("buyer_list_app_id","DESC")->get()->toArray();

        $seller_chat = SellerListingApplicant::where("status","!=","9")
        ->whereHas("sellerlist",function($q) use($seller_id){ $q->where('seller_id',$seller_id); })
        ->whereHas("comments",function($q){ $q->whereNotNull('comment_id'); })
        ->orderBy("sell_list_app_id","DESC")->get()->toArray();

        $chats = array_merge($seller_chat,$buyer_chat);

        $res_data['chats'] = $chats;

        if(count($chats)==0){ $code = 204; $message = "No Chat Found !"; }

        return respondWithSuccess($res_data, 'SELLER', $request_log_id, $message, $code);
    }

      // Web Controller Functions
      public function seller_detailsgWeb(Request $request, $id = null){
        $data = $request->all();
        $system_status = SystemStatus::where('key', 'LIKE', "%".'ACTIVE_INACTIVE'."%")->get();
       
        $type = ['individual'=>"Individual",'establishment'=>"Establishment",'contractor'=>"Contractor",'corporate'=>"Corporate"];
        if(!is_null($id)){
        $sellers = Seller::with('systemstatus', 'addressdetail')->where('seller_id', $id)->orderBy("created_at","DESC")->first();
        return response()->json([
            'status'=>200,
            'sellers'=>$sellers,
        ]);
        }
        $sellers = Seller::with('systemstatus', 'addressdetail');
        if (isset($data['fullname']) && $data['fullname'] != null && $data['fullname'] != "") {
            $sellers->where('fullname', 'LIKE', "%".$data['fullname']."%");
        }
        if (isset($data['type']) && $data['type'] != null && $data['type'] != "") {
            $sellers->where('type', 'LIKE', "%".$data['type']."%");
        }
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $sellers->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
          }
          if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
            $sellers->where('is_verified', $data['is_verify']);
          } 
          if(isset($data['date']) && $data['date'] != null && $data['date'] != ""){
            $date= $data['date'];
            // dd($date);
            $sellers->whereDate('created_at','=',$date);
          }
        $sellers=  $sellers->orderBy("created_at","DESC")->get();
        return view('admin.seller_details',compact('sellers', 'data', 'system_status', 'type'));
    }
    public function seller_Updated(Request $request, $id){
        $validator = Validator::make([
                    
            'seller_id' => $id
        ],[
            'seller_id' => 'int|min:1|exists:seller,seller_id',
  
        ]);
        if ($validator-> fails()){
            return responseValidationError('Fields Validation Failed.', $validator->errors());
        }

        $errors = [];

            $data = $request->all();
            // $request_log_id = $data['request_log_id'];
            // unset($data['request_log_id']);
// dd($data['data']);
            $seller = new Seller();
            $seller = $seller->change($data['data'], $id);

            if (!is_object($seller)) {
                $errors = \App\Message\Error::get('seller.change');
            } 

            if (count($errors) > 0) {
                return response()->json([
                    "code" => 500,
                    "errors" => $errors
                ]);
            }


            return response()->json([
                    'status'=>200,
                    'message'=>'Seller Updated Successfully',
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
    public function seller_dashboard(Request $request, $id = null){
        $data = $request->all();
        $seller = Seller::find($id);
        $systemstatus = SystemStatus::where('key', 'LIKE', "%".'LISTING'."%")->get();
        $sellerAddress =  \DB::select (" select distinct(address) from addresses a inner join seller_listing sl on sl.address_id=a.address_id where sl.seller_id=$id");

        // $sellerAddress = SellerListing::with(['address'])->where('seller_id', $id)->get();
        // dd($sellerAddress);
        // dd( $systemstatus[1]->name);
        $listings = SellerListing::with(['address:address_id,address','material','seller','material','applicants','applicants.offers', 'systemstatus'])->where("seller_id",$id)->where("status",'!=',9);
        if(isset($data['fullname']) && $data['fullname'] != null && $data['fullname'] != ""){
            $fullname = $data['fullname'];
            $listings->whereHas("seller",function($q) use ($fullname ){
                $q->where('fullname', 'LIKE', "%".$fullname."%");
            });
            // dd($listings->get());
        }
        if (isset($data['mname']) && $data['mname'] != null && $data['mname'] != "") {
            $mname = $data['mname'];
            $listings->whereHas('material', function($q) use($mname){
                $q->where('name', 'LIKE', "%".$mname."%");
              });
          
        }
        if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
            $listings->where('is_verified', $data['is_verify']);
          }
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $listings->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
          }  
        $orders = Order::with(["address","driver","material.image","offer.buyer","offer.seller","offer.sellerlisting","offer.sellerlisting.images"])->whereHas("offer.seller", function($query) use($id){
            $query->where('seller_id', $id);
        })
        ->orderBy("order_id", "DESC")->get();

        $payments = Payment::with(["order:order_id,order_no,offer_id,mat_id,driver_id,quantity,quantity_unit,price,total_price","order.driver","order.material:mat_id,sub_cat_id,product_code,name,name_ar,description","order.material.image","order.offer:offer_id,offer_no,seller_id,buyer_id,offered_price,offered_price_with_vat,reason,status","order.offer.buyer","order.offer.seller"])->where("status","!=","9")
        ->whereHas("order.offer.seller",function($q) use ($id){
            $q->where('seller_id', $id);
        });
        $payments = $payments->orderBy("status","ASC")->orderBy("created_at","DESC")->get();

        $listings =  $listings->orderBy("created_at","DESC")->get();
        return view('admin.seller_dashboard',compact('seller', 'listings', 'orders', 'payments', 'systemstatus', 'sellerAddress'));
    }
    public function add_wallet_amount(Request $request, $id=null){
        $data = $request->all();
       $seller = Seller::find($id);
     
       $wallet_amount = $seller->wallet_amount+$data['wallet_amount'];
       $seller->update([
        'wallet_amount' =>$wallet_amount,
        'updated_source'=> 'user',
        'updated_at'=>  date("Y-m-d H:i:s"),
        'updated_by'=> Auth::user()->user_id,
       ]);
       return response()->json([
        'status'=>202,
        'seller'=>$seller,
       ]);
    }
    public function chang_seller_statusweb(Request $request){
        $id = $request->input('id');
      
        $seller=Seller::find($id);
        $seller->update([
            'is_verified' =>  $seller ->is_verified == 0 ? 1 : 0,
            'updated_source'=> 'user',
            'updated_at'=>  date("Y-m-d H:i:s"),
            'updated_by'=> Auth::user()->user_id,

        ]);
      
        return response()->json([
            'status'=>200,
            'seller'=>$seller,
             
        ]);
    }
    public function get_applicant(Request $request, $sell_list_id=null){
        $errors = [];
        $data = $request->json()->all();//$request->all();
        
        // $request_log_id = $data['request_log_id'];
        // unset($data['request_log_id']);

        $res_data = array();
        $code = 200; $message = "Offers Loaded Successfully !";
        // DB::enableQueryLog();
        $offer_arr = Offer::where("status","!=","9")->where("listing_source","seller_listing");
        if(!is_null($sell_list_id)){ $offer_arr = $offer_arr->where("listing_id",$sell_list_id); }
        $offer_arr = $offer_arr->groupBy("application_source")->groupBy("application_id")->pluck('application_id')->toArray();

        $offers = array();

        foreach($offer_arr as $app_id){
            $temp_offer = Offer::with(['sellerlisting','buyer','seller', 'order'])->where("status","!=","9")->where("listing_source","seller_listing");
            $temp_offer = $temp_offer->where("application_id",$app_id)->orderBy("created_at","DESC")->orderBy("listing_id","DESC")->get();
        //    dd(  $temp_offer );
            array_push($offers, $temp_offer[0]);
        }
        // dd($offers);
        // dd(DB::getQueryLog());
        if(count($offers)==0){ $code = 204; $message = "No Offers Available!"; }

        $res_data['offers'] = $offers;
       
        return response()->json([
            'status'=>200,
            'applicants'=>$res_data
        ]);
    
        // if(!is_null($id)){
         
        // $sellerapplicants = SellerListingApplicant::where("sell_list_id",$id)->get();
        // // 
        //  dd($sellerapplicants );
        //     return response()->json([
        //         'status'=>200,
        //         'applicants'=>$sellerapplicants,
        //     ]); 
        // } 
    }
    public function show_listingWeb(Request $request,$sell_list_id=null){  
        $data = $request->all();
        $system_status = SystemStatus::where('key', 'LIKE', "%".'LISTING'."%")->get();
        // DB::enableQueryLog();
        $listings = SellerListing::with(['address','material','seller','material.image','applicants','applicants.offers', 'systemstatus']);
   
        $res_data = array();
       // $sell_list_id = $id;
         
        if(!is_null($sell_list_id)){
            // dd( $sell_list_id );
            // $listings = SellerListing::with(['address','material','material.image','applicants','applicants.offers', 'systemstatus'])->where("created_source",$data['created_source'])->where("created_by",$data['created_by'])->where("sell_list_id",$sell_list_id)->where("status",'!=',9)->orderBy("created_at","DESC")->get();
            $listings = SellerListing::with(['address:address_id,address','material','seller','material','applicants','applicants.offers', 'systemstatus'])->where("sell_list_id",$sell_list_id)->where("status",'!=',9)->orderBy("created_at","DESC")->first();
            $images = $listings->images;
            return response()->json([
                'status'=>200,
                'listing'=>$listings,
                'images'=>$images,
            ]);
          
        }

        if (isset($data['fullname']) && $data['fullname'] != null && $data['fullname'] != "") {
            $fullname = $data['fullname'];
            $listings->whereHas('seller', function($q) use($fullname){
                $q->where('fullname', 'LIKE', "%".$fullname."%");
              });
          
        }
        if (isset($data['mname']) && $data['mname'] != null && $data['mname'] != "") {
            $mname = $data['mname'];
            $listings->whereHas('material', function($q) use($mname){
                $q->where('name', 'LIKE', "%".$mname."%");
              });
           
        }
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $listings->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
          } 
          if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
            $listings->where('is_verified', $data['is_verify']);
          }        
          if(isset($data['date']) && $data['date'] != null && $data['date'] != ""){
            $date= $data['date'];
            // dd($date);
            $listings->whereDate('created_at','=',$date);
          }   
          if(isset($data['day']) && $data['day'] != null && $data['day'] != ""){
            $day= $data['day'];
            // dd($date);
            $listings->where('active_days','=',$day);
          }
            $listings = $listings->orderBy("sell_list_id","DESC")->get();
            
            // dd(DB::getQueryLog());
        return view('admin.selerListing', compact('listings', 'data', 'system_status'));

        
    }
    public function chang_sellerlisting_status(Request $request){
        $data = $request->all();
        $id = $request->input('id');
      
        $listing = SellerListing::find($id);
        $listing->update([
            'is_verified' =>  $listing ->is_verified == 0 ? 1 : 0,
            'updated_source'=> 'user',
            'updated_at'=>  date("Y-m-d H:i:s"),
            'updated_by'=> Auth::user()->user_id,

        ]);

        // Notification Work Zeeshan Qureshi
        $seller = Seller::find($listing->seller_id);
        $notification_id = $seller->fcm_token_for_seller_app;

        $notification_message = "Seller Listing # " .$listing->listing_no." is Approved.";
        $source = 1;
        $is_sent = 0;

        if ($notification_id != "" || $notification_id != null) {
            $title = "Seller Listing Update";
            $type = "basic";
            try {
                $res = send_notification_FCM($notification_id, $title, $notification_message, $type, $source);
                $is_sent = 1;
            } catch (\Exception $ex) { /* error_log($ex->getMessage()); */ }
        }

        $insertNotification = Notification::insert([
            'to_source' => "seller",
            'user_id' => $seller->seller_id,
            'reference_type' => 'Seller Listing',
            'reference_id' => $listing->sell_list_id,
            'notification_body' => $notification_message,
            'is_sent' => $is_sent,
            'created_source' => $data['created_source'],
            'created_by' => $data['created_by'],
            'created_at' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'status'=>200,
            'listing'=>$listing,
             
        ]);
    }
}
