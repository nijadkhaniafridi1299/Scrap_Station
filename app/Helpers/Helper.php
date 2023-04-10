
<?php
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

function cleanNameString($string) {
    $symbols_data = "/[^\p{L}\p{N}\s\-_\[\]\.@\(\)%&]/u";
    $sqlwordlist = array('select','drop','delete','update',' or ','mysql', 'sleep');
    $value = preg_replace($symbols_data, '', $string);
    foreach ($sqlwordlist as $v)
        $value = preg_replace("/\S*$v\S*/i", '', $value);
    return $value;
}

function respondWithSuccess($data, $module, $request_log_id, $message="", $success_code = 200){
  return response()->json([
      "code" => $success_code, "success" => true, "request_log_id" => $request_log_id,
      "module" => $module, "message" => $message, "data" => $data
  ]);
}

function respondWithError($errors,$request_log_id,$error_code=500){
  $err_msg = "";
  foreach($errors as $err){ $err_msg .= (is_array($err)?implode(",",$err):$err).","; }
  $err_msg = rtrim($err_msg,",");
  return response()->json([
      "code" => $error_code, "success" => false, "request_log_id" => $request_log_id, "message" => $err_msg, "errors" => $errors
  ]);
}

function responseValidationError($message, $errors){

    return response([

        'status' => 'error',
        'code' => '400',
        'message' => $message,
        'data' => $errors

    ]);

}

function getIp(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
    return request()->ip(); // it will return server ip when no client ip found
}

function send_notification_FCM($notification_id, $title, $message,$type,$source, $additional_data=null) {
  if($notification_id != null){
       $reg_id = $notification_id;
       //Source 0 For Web 
       
       if($source==0){
           $dataArray = array(
             'reference_id' => 1,
             'key' => $type,
           );
           
           $message = [
             "to" => $reg_id,
             "data"=>[
               "message"  => $title,
               "body" => $message
             ]
            
           ];
           
           if($additional_data!=null){
             $message["data"] = $message["data"] + $additional_data;
           }
           $client = new GuzzleHttp\Client([
             'headers' => [
                 'Content-Type' => 'application/json',
                 'Authorization' => 'key=AAAAVYvjeE8:APA91bHlsLQOSvxlAEyRyZGIfLwmWHPy0adX_xhcdaHf0OXjOH652d1FRjptQd5ypuZu6ColciC7w4u1AzkzdfpiBBgU_AT3Dg5walwiMA4y8Z1XcagL_YlXKlHLPOTbgxf2b4ZQi8n8',
             ]
           ]);
       }
       //Source 1 For Mobile
       else if($source==1){
           $message = [
             "registration_ids" => array($notification_id),
             "notification" => [
                 "title" => $title,
                 "body" => $message,
             ]
           ];
           $client = new GuzzleHttp\Client([
             'headers' => [
                 'Content-Type' => 'application/json',
                 'Authorization' => 'key=AAAAVYvjeE8:APA91bHlsLQOSvxlAEyRyZGIfLwmWHPy0adX_xhcdaHf0OXjOH652d1FRjptQd5ypuZu6ColciC7w4u1AzkzdfpiBBgU_AT3Dg5walwiMA4y8Z1XcagL_YlXKlHLPOTbgxf2b4ZQi8n8',
             ]
           ]);
       }
       $response = $client->post('https://fcm.googleapis.com/fcm/send',
           ['body' => json_encode($message)]
       );
    }
 }

 function filterMobileNumber($mobile_no){
  if(is_numeric($mobile_no)){
    $number_length = strlen($mobile_no);
    if($number_length == 9){ return "966".$mobile_no; }
    if($number_length == 10){ return "966".ltrim($mobile_no,"0"); }
    if($number_length == 12){ return $mobile_no; }
  }
  return "";
}

 function paginate($items, $perPage = null, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
}

  function group_by($key, $data) {
    $result = array();

    foreach($data as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }
        return $result;
}

function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
{
  $i = $j = $c = 0;
  for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
    if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
     ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
       $c = !$c;
  }
  return $c;
}  

function callExternalAPI($method,$url,$body,$headers){
  $client = new GuzzleHttp\Client([ 'headers' => $headers ]);
  $response = null; $return_data = "";
  switch($method){
    case "POST": $request = $client->post($url, ['body' => json_encode($body)] ); $response = $request->send(); break;
    default: $request = $client->get($url); $response = $request->getBody(); break;
  }
  while (!$response->eof()) { $return_data .= $response->read(1024); }
  
  return $return_data;
}

function sanitizeData($all_data){
  foreach($all_data as $ky=>$data){ if(empty($data)){ $all_data[$ky] = null;} }
  return $all_data;
}

function prepareImageData($ref_type,$img_path,$data,$note=""){
  $img_data = [
    "ref_type" => $ref_type,
    "path" => $img_path,
    "note" => $note,
    "created_source" => $data['created_source'],
    "created_by" => $data['created_by'],
    "created_at" => date("Y-m-d H:i:s")
  ];
  return $img_data;
}

function generateNumber($model_name, $dependant_col_name, $dependant_id){
  $counter = 1;

  $order_number = $dependant_id ; //str_pad($customer_id, 3, "0", STR_PAD_LEFT);
  //$order_number .= time();
  $model = "App\\Model\\".$model_name;
  $order = $model::where($dependant_col_name, $dependant_id)->count();
  $counter += $order;

  return $order_number . str_pad($counter, 3, '0', STR_PAD_LEFT);
}
function getGroupBasedOptions($group_name){
  $data = DB::select("SELECT oc.option_name AS `Name`,oc.option_key AS `Value` FROM `options` op LEFT JOIN `options` oc ON oc.parent_id=op.option_id WHERE op.option_group='".$group_name."';");
  return $data;
}