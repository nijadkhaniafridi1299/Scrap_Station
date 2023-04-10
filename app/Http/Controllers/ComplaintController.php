<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Complaint;
use App\Message\Error;
use App\Model\Order;
use App\Model\Payment;
use App\Model\Comment;
use App\Model\Seller;
use App\Model\Buyer;
use App\Model\User;
use App\Model\Notification;
use App\Model\SystemStatus;
use Auth;
class ComplaintController extends Controller
{
    public function addComplaint(Request $request)
    {
        $errors = [];
        $data = $request->json()->all();
        if (count($data) == 0) { $data = $request->all(); }
        $request_log_id = $data['request_log_id'];
       
        unset($data['request_log_id']);
        $complaint = new Complaint();
        
        $data['created_source'] =  $data['created_source'];
        $data['created_by'] =  $data['created_by'];
        $data['created_at'] =  date('Y-m-d H:i:s');

        $created_source = $data['created_source'];

        $complaint = $complaint->add($data);
        $complaint_no = generateNumber("Complaint", "seller_id", $data['created_by']);
        $complaint->complaint_no = $complaint_no;
        $complaint->save();

        if (!is_object($complaint)) {
            $errors = \App\Message\Error::get('complaint.add');
        }

        if (isset($errors) && count($errors) > 0) {
            return respondWithError($errors,$request_log_id);
        }

        $seller_id = $data['created_by'];
        $seller = Seller::find($seller_id);
        $seller_fcm_token = $seller->seller_fcm_token;
        $title = "Complaint";
        $message = "Seller ".$seller->fullname." submitted complaint on ".$complaint->item_source." ".$complaint->item_id;
        $ref_id = $complaint->complaint_id;

        if($created_source == "user" || $created_source == "buyer"){
            $is_sent = 0;
            $type = "basic";
            if ($seller_fcm_token != "" || $seller_fcm_token != null) {
                $notification_id = $seller_fcm_token;
                $source=1;
                $res = send_notification_FCM($notification_id, $title, $message, $type,$source);
                $is_sent = 1;
            } 
            $insertNotification = Notification::insert([
                'to_source' => "seller",
                'user_id' => $seller_id,
                'reference_type' => 'complaint',
                'reference_id' => $ref_id,
                'notification_body' => $message,
                'is_sent' => $is_sent,
                'created_source' => $data['created_source'],
                'created_by' => $data['created_by'],
            ]);
        }
        if($created_source == "seller" || $created_source == "buyer"){
            $getUsers = User::join('groups','users.group_id','=','groups.group_id')
            ->where('groups.role_key','ADMIN')->get()->toArray();
            $save_Notification = array();
            $type = "basic";
            for ($i = 0; $i < count($getUsers); $i++) {
                $is_sent = 0;
                $notification_id =  User::where('user_id', $getUsers[$i]['user_id'])->value('fcm_token_for_web');
                if ($notification_id != "" || $notification_id != null) {
                    $res = send_notification_FCM($notification_id, $title, $message, $type, "0");
                    $is_sent = 1;
                }
                $save_Notification[] = [
                    'to_source' => "user",
                    'user_id' => $getUsers[$i]['user_id'],
                    'reference_id' => $ref_id,
                    'reference_type' => "complaint",
                    'notification_body' => $message,
                    'is_sent' => $is_sent,
                    'created_source' => $data['created_source'],
                    'created_at' =>  date('Y-m-d H:i:s'),
                    'created_by' => $data['created_by'],
                ];
            }
            if(count($save_Notification)>0){
                Notification::insert($save_Notification);
            }
        }

        return response()->json([
            "code" => 201,
            "complaint" => $complaint,
            "success" => true,
            "message" => 'New Complaint has been registered against '.$complaint->item_source.".",
            "module" => 'COMPLAINT',
            "request_log_id" => $request_log_id
        ]);
    }

    public function getComplaintElements(Request $request){
        $errors = [];
        $data = $request->json()->all();
        if (count($data) == 0) { $data = $request->all(); }
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $orders = null;
        if(isset($data['seller_id'])){
            $seller_id = $data['seller_id'];
            $orders = Order::whereHas('offer', function($q) use($seller_id){
                $q->where('seller_id', '=', $seller_id);
            })->select("order_id AS id","order_no AS name")->get();
        }
        else{
            $orders = Order::select("order_id AS id","order_no AS name")->get();
        }

        $pay_reqs = null;
        if(isset($data['seller_id'])){
            $pay_reqs = Payment::where("created_source","seller")->where("created_by",$data['seller_id'])->select("pay_id AS id","pay_no AS name")->get();
        }
        else{
            $pay_reqs = Payment::select("pay_id AS id","pay_no AS name")->get();
        }

        $res_data = array();
        $res_data['orders'] = $orders;
        $res_data['payments'] = $pay_reqs;

        if (isset($errors) && count($errors) > 0) {
            return respondWithError($errors,$request_log_id);
        }

        return respondWithSuccess($res_data, 'COMPLAINT', $request_log_id, "");
    }

    public function getComplaints(Request $request, $sup_id = null ){
        $errors = [];
        $data = $request->json()->all();
        if (count($data) == 0) { $data = $request->all(); }
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $seller_id = null;

        if($sup_id != null){ $seller_id = $sup_id; }
        else if(isset($data['seller_id']) && !empty($data['seller_id'])){ $seller_id = $data['seller_id']; }

        $main_data = array();

        $complaints = Complaint::with("history")->get();
        if($seller_id != null){
            $complaints = Complaint::with('history')->where("created_source","seller")->where("created_by",$seller_id)->get();
        }

        $main_data = $complaints;
        foreach($complaints as $ky_comp => $comp){
            $main_data[$ky_comp]['assigned_by_person'] = ($comp->assigned_by_source !=null ?$comp->assignedByPerson:null);
            if(count($comp->history)>0){
                foreach($comp->history as $ky_hist=>$hist){
                    $main_data[$ky_comp]['history'][$ky_hist]['assigned_by_person'] = ($hist->assigned_by_source !=null ?$hist->assignedByPerson:null);
                }
            }
        }
        foreach($complaints as $ky_comp => $comp){
            $main_data[$ky_comp]['assigned_to_person'] = ($comp->assigned_to_source !=null ?$comp->assignedToPerson:null);
            if(count($comp->history)>0){
                foreach($comp->history as $ky_hist=>$hist){
                    $main_data[$ky_comp]['history'][$ky_hist]['assigned_to_person'] = ($hist->assigned_to_source !=null ?$hist->assignedToPerson:null);
                }
            }
        }

        if (isset($errors) && count($errors) > 0) {
            return respondWithError($errors,$request_log_id);
        }

        $res_data = array();

        $res_data['complaints'] = $main_data;

        return respondWithSuccess($res_data, 'COMPLAINT', $request_log_id, "");
    }
    //web api
    public function complaintListWeb(Request $request, $id = null){
        $data = $request->all();
        $system_status = SystemStatus::where('key', 'LIKE', "%".'ACTIVE_INACTIVE'."%")->get();
        if(!is_null($id)){
           $complaint =  Complaint::with( 'seller', 'systemstatus')->where('complaint_id', $id)->first();
           $complaint_history = $complaint->history;
           $complaint_comments = $complaint->comments;
           foreach($complaint_comments as $cm_p){

           
             $cm_p->images;
           }
           return response()->json([
            'status'=>200,
            'complaint'=>$complaint,
            'complaint_history'=>$complaint_history,
            'complaint_comments'=>$complaint_comments,
           
           ]);
        }
        $complaint = Complaint::with('history', 'comments');
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            
            $complaint->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
        }
        if(isset($data['date']) && $data['date'] != null && $data['date'] != ""){
            $date= $data['date'];
            // dd($date);
            $complaint->whereDate('created_at','=',$date);
           
        }
        if(isset($data['source']) && $data['source'] != null && $data['source'] != ""){
            $source= $data['source'];
            // dd($date);
            $complaint->where('item_source', 'LIKE', "%".$source."%");
           
        }
        $source_list = getGroupBasedOptions('Complaint Source');
        // dd( $source);
        $complaint = $complaint->get();
        // dd($complaint);
        return view('admin.complaint', compact('complaint', 'data', 'source_list', 'system_status'));
    }
    public function complaint_changeStatusWeb(Request $request, $id = null){
        $data = $request->all();
        
        $status_change = Complaint::find($id);
        $status_change->update([
            'status'=>$status_change->status== 1 ? 9 : 1,
            'reason'=>$data['reason'],
            'updated_source'=> 'user',
            'updated_at'=>  date("Y-m-d H:i:s"),
            'updated_by'=> Auth::user()->user_id,
        ]);
        return response()->json([
            'status'=>200,
            'status_change'=>$status_change,
        ]);
    }
}
