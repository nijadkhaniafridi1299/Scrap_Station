<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Buyer;
use App\Model\Driver;
use App\Model\SystemStatus;
use Validator;
use Auth;
class DriverController extends Controller
{
    //web function
    public function driverListsWeb(Request $request, $driver_id = null)
    {
        $data = $request->all();
        $buyerlist = Buyer::select('buyer_id', 'fullname')->where("status",'!=',9)->get();
        $system_status = SystemStatus::where('key', 'LIKE', "%".'ACTIVE_INACTIVE'."%")->get();
  
        $driver= Driver::with('buyer', 'systemstatus');
        if(!is_null($driver_id)){
         $driver= Driver::with('buyer', 'systemstatus')->where('driver_id', $driver_id)->first();
         return response()->json([
          'statu'=>200,
          'driver'=>$driver
        ]);
        }
        if (isset($data['fullname']) && $data['fullname'] != null && $data['fullname'] != "") {
            $driver->where('fullname', 'LIKE', "%".$data['fullname']."%");
        }
        if(isset($data['date']) && $data['date'] != null && $data['date'] != ""){
            $date= $data['date'];
            // dd($date);
            $driver->whereDate('created_at','=',$date);
           
        }
        if(isset($data['buyername']) && $data['buyername'] != null && $data['buyername'] != ""){
            $buyername= $data['buyername'];
            // dd($buyername);
            $driver->whereHas('buyer', function($q) use($buyername){
                $q->where('fullname',   'LIKE', "%".$buyername."%");
              });
           
        } 
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $driver->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
        } 
        if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
            $driver->where('is_verified', $data['is_verify']);
          }
        $driver = $driver->get();
        return view('admin.driver', compact('driver', 'system_status', 'data', 'buyerlist'));
        

    }
    public function driververified(Request $request){
        $driver_id = $request->input('id');
         $driver = Driver::find($driver_id);
         $driver->update([
            'is_verified' =>  $driver->is_verified == 0 ? 1 : 0,
            'updated_source'=> 'user',
            'updated_at'=>  date("Y-m-d H:i:s"),
            'updated_by'=> Auth::user()->user_id,

         ]);
         return response()->json([
            'status' => 200,
            'message'=> 'Driver verified Successfully',
            'driver'=> $driver
         ]);
    }
    public function add_driverWeb(Request $request)
    {
        $errors = [];
        $data = $request->all();
        
        $password ='welcome';
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);
        $driver = new Driver();
      
        //step1: create a new buyer in buyers
        $data['driver']['created_at'] =  date('Y-m-d H:i:s');

        if(isset($data['created_by'])){ $data['driver']['created_by'] = $data['created_by']; }
        if(isset($data['created_source'])){ $data['driver']['created_source'] = $data['created_source']; }

        $data['driver'] = sanitizeData($data['driver']);
    
        if(!empty($data['driver']['mobile']) && is_numeric($data['driver']['mobile'])){
            $data['driver']['mobile'] = filterMobileNumber($data['driver']['mobile']);
        }
    //  dd($data['driver']);
        
        $data['driver']['password'] = $password;
        $data['driver']['status'] = 1;

        $driver = new Driver();
        $validated_driver = $driver->validateAtStart($data['driver']);
    
        if (!$validated_driver ) {
            array_push($errors, \App\Message\Error::get('driver.start'));
        }
     
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }
     
        $driver = $driver->add($data['driver']);
        
        if (!is_object($driver)) {
            $errors = \App\Message\Error::get('driver.add');
        }

        if (isset($errors) && count($errors) > 0) {
            
            return respondWithError($errors,$request_log_id); 
        }
        return response()->json([
            'status'=>200,
            'message'=>'Driver Added Successfully!',
        ]);
    }

    public function driver_update_Web(Request $request, $id = null){
        
        $data = $request->all();

        $validator = Validator::make([
                    
            'driver_id' => $id
        ],[
            'driver_id' => 'int|min:1|exists:driver,driver_id',
  
        ]);
        if ($validator-> fails()){
            return responseValidationError('Fields Validation Failed.', $validator->errors());
        }

        $errors = [];
        
        $data['driver'] = sanitizeData($data['driver']);

        if(!empty($data['driver']['mobile']) && is_numeric($data['driver']['mobile'])){
            $data['driver']['mobile'] = filterMobileNumber($data['driver']['mobile']);
        }
        $driver= new Driver();
        $driver = $driver->change($data['driver'], $id);

        if (!is_object($driver)) {
            $errors = \App\Message\Error::get('driver.change');
        } 

        if (count($errors) > 0) {
            return response()->json([
                "code" => 500,
                "errors" => $errors
            ]);
        }


        return response()->json([
                'status'=>200,
                'message'=>'Driver Updated Successfully',
            ]);
    }
}
