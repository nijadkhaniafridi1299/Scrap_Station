<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\User ;
use  App\Model\Option;
use  App\Model\Order;
use  App\Model\Location;
use  App\Model\Customer;
use DB;

class OptionController extends Controller
{
public function getCompanySettings(Request $request){

$data =  json_decode($request->getContent(),true);

// if($data == ""){
//   return response()->json([
//     "Code" => 403,
//     "Message" => "Invalid json."
//   ]);
// }

// if(!isset($data['location_date'])){
//   return response()->json([
//     "Code" => 403,
//     "Message" => "location_date missing."
//   ]);
// }

$feedback_order = '';
$feedback_order_number = '';
$force_change_password = 0;
if(isset($data['user_id']) && $data['user_id'] != Null && $data['user_id'] != ""){
  $feedback_order_data = Order::where('customer_id',$data['user_id'])->where('order_status_id',4)->where('rating_created_at', Null)->where('enable_feedback',1)->get()->last();
  
  if(is_object($feedback_order_data)){
    $feedback_order = $feedback_order_data->order_id;
    $feedback_order_number = $feedback_order_data->order_number;
  }
  else{
    $feedback_order = '';
    $feedback_order_number = '';
  }
  $pass_data = Customer::select(['force_change_password','account_type_id','channel_id'])->find($data['user_id']);
  if(isset($pass_data->force_change_password) && $pass_data->account_type_id != 0){
    $force_change_password = $pass_data->force_change_password;
  }
}

// $options = Option::select('parent_id','option_key','option_value')->get()->toArray();

$options = DB::table('options as t1')
    ->leftJoin('options AS t2', 't2.option_id', '=', 't1.parent_id')
    ->select('t1.option_key','t1.option_value','t1.option_group','t2.option_group as parent_group')
    ->get();

    $options = json_decode(json_encode($options),true);

$min_weight = '';
$min_weight_unit = '';
$min_amount = '';
$vat = '';
$help_line = '920025555';
$whats_app = '920025555';
$email = 'smtayyeb@gmail.com';
$is_cash_delivery_enable = '';
$is_credit_card_enable = '';
$is_sadad_enable = '';
$is_applepay_enable = '';
$cur_android_ver = '';
$cur_ios_ver = '';
$loadcityarea = '';
$last_area_updated_date = '';
$is_ticket_enable = '';
$delivery_fee = '';
$show_vat = "0";
$enable_other_channels = '';
$enable_mosque = '';
$show_category = "1";
$tc = "";
$tc_en = "";
$tc_ar = "";
$facebook = "";
$twitter = "";
$instagram = "";
$snapchat = "";
$show_address_change_popup = 0;
$hide_category_name = 0;
$material_units = array();
$submission_statuses = array();
$complaint_sources = array();
$complaint_type = array();
$seller_listing_limit = "";
$buyer_listing_limit = "";
////////

for ($i=0, $count = count($options); $i < $count; $i++) {
  $min_weight = $options[$i]['option_key'] == 'MIN_WEIGHT' ? $options[$i]['option_value'] : $min_weight;
  $min_weight_unit = $options[$i]['option_key'] == 'MIN_WEIGHT_UNIT' ? $options[$i]['option_value'] : $min_weight_unit;
  $min_amount = $options[$i]['option_key'] == 'MIN_AMOUNT' ? $options[$i]['option_value'] : $min_amount;
  $vat = $options[$i]['option_key'] == 'VAT_IN_PERCENT' ? $options[$i]['option_value'] : $vat;
  $help_line = $options[$i]['option_key'] == 'HELP_LINE' ? $options[$i]['option_value'] : $help_line;
  $is_cash_delivery_enable = $options[$i]['option_key'] == 'CASH_ON_DELIVERY' ? $options[$i]['option_value'] : $is_cash_delivery_enable;
  $is_credit_card_enable = $options[$i]['option_key'] == 'MOYASAR_CREDIT_CARD' ? $options[$i]['option_value'] : $is_credit_card_enable;
  $is_applepay_enable = $options[$i]['option_key'] == 'APPLE_PAY' ? $options[$i]['option_value'] : $is_applepay_enable;
  $cur_android_ver = $options[$i]['option_key'] == 'CURRENT_ANDROID_VERSION' ? $options[$i]['option_value'] : $cur_android_ver;
  $cur_ios_ver = $options[$i]['option_key'] == 'CURRENT_IOS_VERSION' ? $options[$i]['option_value'] : $cur_ios_ver;
  $loadcityarea = $options[$i]['option_key'] == 'LOAD_CITY_AREA' ? $options[$i]['option_value'] : $loadcityarea;
  $last_area_updated_date = $options[$i]['option_key'] == 'LAST_AREA_UPDATE_DATE' ? $options[$i]['option_value'] : $last_area_updated_date;
  $is_ticket_enable = $options[$i]['option_key'] == 'IS_TICKET_ENABLE' ? $options[$i]['option_value'] : $is_ticket_enable;
  $delivery_fee = $options[$i]['option_key'] == 'DELIVERY_FEE' ? $options[$i]['option_value'] : $delivery_fee;
  $whats_app = $options[$i]['option_key'] == 'WHATS_APP' ? $options[$i]['option_value'] : $whats_app;
  $enable_other_channels = $options[$i]['option_key'] == 'ENABLE_OTHER_CHANNELS' ? $options[$i]['option_value'] : $enable_other_channels;
  $enable_mosque = $options[$i]['option_key'] == 'ENABLE_MOSQUE_ORDERS' ? $options[$i]['option_value'] : $enable_mosque;
  $email = $options[$i]['option_key'] == 'EMAIL' ? $options[$i]['option_value'] : $email;
  $show_category = $options[$i]['option_key'] == 'SHOW_CATEGORY' ? $options[$i]['option_value'] : $show_category;
  $tc = $options[$i]['option_key'] == 'TERMS_CONDITIONS' ? $options[$i]['option_value'] : $tc;
  $facebook = $options[$i]['option_key'] == 'FACEBOOK' ? $options[$i]['option_value'] : $facebook;
  $twitter = $options[$i]['option_key'] == 'TWITTER' ? $options[$i]['option_value'] : $twitter;
  $instagram = $options[$i]['option_key'] == 'INSTAGRAM' ? $options[$i]['option_value'] : $instagram;
  $snapchat = $options[$i]['option_key'] == 'SNAPCHAT' ? $options[$i]['option_value'] : $snapchat;
  $show_address_change_popup = $options[$i]['option_key'] == 'SHOW_ADDRESS_POPUP' ? $options[$i]['option_value'] : $show_address_change_popup;
  $show_vat = $options[$i]['option_key'] == 'SHOW_VAT' ? $options[$i]['option_value'] : $show_vat;
  $seller_listing_limit = $options[$i]['option_key'] == 'seller_listing_age_limit' ? $options[$i]['option_value'] : $seller_listing_limit;
  $buyer_listing_limit = $options[$i]['option_key'] == 'buyer_listing_age_limit' ? $options[$i]['option_value'] : $buyer_listing_limit;
  $hide_category_name = $options[$i]['option_key'] == 'HIDE_CATEGORY_NAME' ? $options[$i]['option_value'] : $hide_category_name;
  if($options[$i]['parent_group'] == 'Unit List') { array_push($material_units,$options[$i]['option_value']); }
  if($options[$i]['parent_group'] == 'Submission Status') { array_push($submission_statuses,$options[$i]['option_value']); }
  if($options[$i]['parent_group'] == 'Complaint Source') { array_push($complaint_sources,$options[$i]['option_value']); }
  if($options[$i]['parent_group'] == 'Complaint Type') { array_push($complaint_type,$options[$i]['option_value']); }

}

// if(isset($pass_data->channel_id)){
//   $min_amount = $min_amount = Option::getValueByKey('MIN_QUANTITY_'.$pass_data->channel_id);
// }

if(!empty($data['location_date']) && ($last_area_updated_date != $data['location_date'])) {
  $area = Location::where("location_level_id", 2)->where("status", 1)->get()->toArray();
  if(count($area) > 0){
    $count = count($area);
    for($i=0; $i<$count;$i++){
      $loc[$i] = json_decode($area[$i]['location_name'], true);
      if(!isset($area[$i]['gmap_meta']) || empty($area[$i]['gmap_meta'])){
        $googleLoc[$i]['ar'] = "";
        $googleLoc[$i]['en'] = "";
      }else{
        $googleLoc[$i] = json_decode($area[$i]['gmap_meta'], true);
      }
      $areas[] = [
        "area_id"=> $area[$i]['location_id'],
        "google_area_en"=> isset($googleLoc[$i]['en'])?$googleLoc[$i]['en']:'',
        "google_area_ar"=> isset($googleLoc[$i]['ar'])?$googleLoc[$i]['ar']:'',
        "area_country_id"=> 1,
        "area_city_id"=> $area[$i]['parent_id'],
        "area_title_ar"=> $loc[$i]['ar'],
        "area_title_en"=> $loc[$i]['en'],
        "area_active"=> $area[$i]['status'],
        "area_code"=> $area[$i]['area_code']
      ];
    }
  }
  else {
    $areas = [];
  }
  $city = Location::where("location_level_id", 1)->where("status", 1)->get()->toArray();
  if(count($city) > 0){
    $count = count($city);
    for($i=0; $i<$count;$i++){
      $loc[$i] = json_decode($city[$i]['location_name'], true);
      $cities[] = [
        "city_id"=> $city[$i]['location_id'],
        "city_country_id"=> 1,
        "city_title_en"=> $loc[$i]['en'],
        "city_title_ar"=> $loc[$i]['ar'],
        "plant"=> $city[$i]['plant'],
        "city_active"=> $city[$i]['status']
      ];
    }
  }
  else {
    $cities = [];
  }
}
else {
  $areas = [];
  $cities = [];
}

if($tc != ''){
  $tc = json_decode($tc,true);
}

$rows = [
  "min_weight"=>$min_weight,
  "min_weight_unit"=>$min_weight_unit,
  "min_amount"=>$min_amount,
  "vat"=>$vat,
  "help_line"=>$help_line,
  "cur_android_ver"=>$cur_android_ver,
  "cur_ios_ver"=>$cur_ios_ver,
  "loadcityarea"=>$loadcityarea,
  "last_area_updated_date"=> (!empty($data['location_date']) && ($last_area_updated_date != $data['location_date'])) ? $last_area_updated_date : '',
  "is_ticket_enable"=>$is_ticket_enable,
  "whats_app"=>$whats_app,
  "delete_last_order_address" => "0",
  "delete_addresses" => "0",
  "show_vat"=>$show_vat,
  "enable_other_channels"=>0,
  "feedback_order"=>$feedback_order,
  "feedback_order_number"=>$feedback_order_number,
  "base_api_url" => "",
  "brand_image" => "",
  "full_page_promo" => "", // based on current language
  "full_page_promo_ar" => "", // based on current language /promo/yaa-general-promo-ar.png
  "full_page_promo_en" => "", // based on current language
  "enable_mosque" => $enable_mosque,
  "stc_qitaf_msg_en"=>"",
  "stc_qitaf_msg_ar"=>"",
  "whats_app"=>$whats_app,
  "email"=>$email,
  "show_category"=>$show_category,
  "force_change_password"=>$force_change_password,
  "change_password_message_en"=>'Please change your password',
  "change_password_message_ar"=>'الرجاء تغيير كلمة المرور الخاصة بك',
  "tc_en" => isset($tc['en'])?$tc['en']:'',
  "tc_ar" => isset($tc['ar'])?$tc['ar']:'',
  "facebook"=>$facebook,
  "twitter"=>$twitter,
  "instagram"=>$instagram,
  "snapchat"=>$snapchat,
  "show_address_change_popup" => $show_address_change_popup,
  "hide_category_name" => $hide_category_name,
  "areas" => $areas,
  "cities" => $cities,
  "seller_listing_limit" => $seller_listing_limit,
  "buyer_listing_limit" => $buyer_listing_limit,
  "material_units" => $material_units,
  'submission_statuses' => $submission_statuses,
  'complaint_sources' => $complaint_sources,
  'complaint_type' => $complaint_type,

];
return response()->json([
  "Code" => 200,
  "rows"=>$rows
]);
}
}