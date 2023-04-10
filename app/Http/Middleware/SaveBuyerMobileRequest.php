<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Config;
use App\Model\TempRequest;


class SaveBuyerMobileRequest
{
	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	protected $web;
	protected $androidDevices;
	protected $iOSDevices;
	protected $postMan;
	protected $deviceType;

	function __construct(){

		// $this->byPassAuthenticationRoute = ['refreshToken','payment-callback'];
	
		$this->web = ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36'];

		$this->androidDevices = ['okhttp', 'android'];
	
		$this->iOSDevices = ['iOS','iphone','ipad'];
	
		$this->postMan = 'PostmanRuntime';

		$this->deviceType = null;
	}
	public function handle($request, Closure $next)
	{
		$route = $request->route();

		if(is_array($route)){
			list($controller, $method) = explode('@', $route[1]['uses']);
		}
		else{
			list($controller, $method) = explode('@', $route->action['uses']);
		}

		$body = $request->getContent();//json_encode($request->getContent(), JSON_UNESCAPED_UNICODE);

		$user = Auth::guard('buyer')->user();
		$data['client_id'] = (isset($user)) ? $user->buyer_id : 0;
		$data['app_source'] = "buyer";
		$data['body'] = $body;
		$data['fname'] = $method;
		// $data['controller'] = $controller;
		$data['device_type'] = (isset($data['order_source_id']))?$data['order_source_id']:$this->getDeviceType();
		$data['api_ver'] = 1.1;
		$data['record_status'] = 1;
		
		$fr_id = "";
		if($method != "getAllNotifications"){
			$fleet_request = new \App\Model\MobileRequest();
			$fleet_request = $fleet_request->add($data);
			$fr_id = $fleet_request->id;
		}

		//$request->session()->put('request_log_id', $fleet_request['id']);
		//$request["request_log_id"] => $fleet_request->id]);

		$modules = \App\Model\FleetModule::get()->pluck('key')->toArray();
		//echo print_r($modules, true); exit;

		$splitted_arr = explode("\\", $controller);
		$cont = $splitted_arr[count($splitted_arr) - 1];
		$cont = strtolower($cont);
		$cont = str_replace('controller', '', $cont);
		
		foreach($modules as $module) {
			if ($cont === strtolower($module)) {
				$request->merge(['request_log_id' => $fr_id]);
			}
		}
		
		
		return $next($request);
	}

	function clean_sqlwords($value) {
		$SYMBOLS_DATA= Config::get('auth.SYMBOLS_DATA'); //print_r($SYMBOLS_DATA) ;echo " - "; print_r($value);
		$sqlwordlist = array('select','drop','delete','update',' or ','mysql', 'sleep');
		$value = preg_replace($SYMBOLS_DATA, '', $value);
		foreach ($sqlwordlist as $v)
		$value = preg_replace("/\S*$v\S*/i", '', $value);
		return $value;
	}

	public function getDeviceType()
	{
		//$_SERVER['HTTP_USER_AGENT'] = "Aseel/1.2.0 (com.alaseeldates; build:1.2.0; iOS 12.1.4) Alamofire/4.8.2";
		//$_SERVER['HTTP_USER_AGENT'] = "okhttp/3.10.0";

		/* Detect Android Device - Start */
		for($i=0; $i < count($this->androidDevices) ; $i++){
			$android = strpos($_SERVER['HTTP_USER_AGENT'],$this->androidDevices[$i]);

			if(($android !== false)){
			return $this->deviceType = 9;
			}
		}

		/* Detect Android Device - End */

		/* Detect iOS Device - Start */
		for($i=0; $i < count($this->iOSDevices) ; $i++){
			$ios= strpos($_SERVER['HTTP_USER_AGENT'],$this->iOSDevices[$i]);

			if(($ios !== false)){
			return $this->deviceType = 8;
			}
		}
		/* Detect iOS Device - End */

		/* Detect Web - Start */
		for ($i=0; $i < count($this->web) ; $i++) {
			$web = strpos($_SERVER['HTTP_USER_AGENT'], $this->web[$i]);

			if (($web !== false)) {
				return $this->deviceType = 1;
			}
		}
		/* Detect Web - End */

		/* Detect Postman - Start */
		// dd($_SERVER['HTTP_USER_AGENT']);
		// dd($this->postMan);
		$postman = strpos($_SERVER['HTTP_USER_AGENT'],$this->postMan);
		if(($postman !== false)){
			return $this->deviceType = 6;
		}
		/* Detect Postman - End */

		/* Detect Devices other than android or iOS - Start */
		if($this->deviceType == NULL){
			return $this->deviceType = 9;
			// return $this->deviceType = $_SERVER['HTTP_USER_AGENT'];
		}

	}

	public function terminate($request, $response) {
	}

		public function saveMobileRequest($data,$funName,$api_ver='1.1',$mobile_brand)
	{
		$body = json_encode($data,JSON_UNESCAPED_UNICODE);
		$parm = ["client_id" => $this->getUserIdParam($data),
		"body" => $body,
		"app_source" => "buyer",
		"fname" => $funName,
		"device_type" => (isset($data['order_source_id']))?$data['order_source_id']:$this->getDeviceType(),
		"api_ver" => $api_ver,
		"transaction_id" => isset($data['transaction_id']) ? $data['transaction_id'] : Null,
		"record_status" => 0,
		"mobile_brand" => isset($mobile_brand)?$mobile_brand:Null
	];

	$apiReq = new TempRequest();
	$reqRes = $apiReq->AddRequest($parm);
	if(!empty($reqRes)){
		return $reqRes->id;
	}


	}

public function getUserIdParam($data){
	if(isset($data['user_id'])){
	  return $this->clean_sqlwords($data['user_id']);
	}
	else if(isset($data['client_id'])){
	  return $this->clean_sqlwords($data['client_id']);
	}
	else if(isset($data['buyer_id'])){
	  return $this->clean_sqlwords($data['buyer_id']);
	}
	else if(isset($data['supervisor_id'])){
	  return $this->clean_sqlwords($data['supervisor_id']);
	}
	else{
	  return 0;
	}
  }
	
  public function updateReqRespone($reqId){
	$apiReq = new TempRequest();
	$reqRes = $apiReq->UpdateRequest($reqId);
	if(!$reqRes){
	  $errors[] = Error::get('request.status');
	}
  }
}