<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Notification;
use App\Message\Error;

class NotificationController extends Controller
{
    public function getAllNotifications(Request $request)
    {
        $errors = [];
        $data = $request->json()->all();
        if(count($data)==0){ $data = $request->all(); }
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $res_data = array();

        $notifications = Notification::where('user_id',$data['created_by'])->where("to_source",$data['created_source'])->orderBy("is_read")->orderBy("created_at","DESC")->select(["notification_id","to_source","user_id","reference_type","reference_id","notification_body","is_read","created_source","created_by","created_at"])->get()->toArray();

        $read_notifications = Notification::where('user_id',$data['created_by'])->where("is_read",0)->where("to_source",$data['created_source'])->select(["notification_id","to_source","user_id","reference_type","reference_id","notification_body","is_read","created_source","created_by","created_at"])->get()->toArray();

        if (isset($errors) && count($errors) > 0) {
            return respondWithError($errors,$request_log_id,404);
        }

        $res_data['notifications'] = $notifications;
        $res_data['total_count'] = count($notifications);
        $res_data['unread_count'] = count($read_notifications);
        // count to be return additionally
        return respondWithSuccess($res_data, 'SUPPLIER', $request_log_id, "");
    }

    public function setNotificationAsRead(Request $request, $notification_id){
        $errors = [];
        $data = $request->json()->all();
        if(count($data)==0){ $data = $request->all(); }
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $notify = Notification::find($notification_id);

        if (!is_object($notify)) {
            Error::trigger("notifications.list", ["Notification List is empty."]);
            $errors = \App\Message\Error::get('notifications.list');
        }

        if (isset($errors) && count($errors) > 0) {
            return respondWithError($errors,$request_log_id,203);
        }

        // find($notification_id);
        $notify->is_read = 1;
        $notify->save();
        $res_data = array();
        $res_data['notification'] = $notify;

        return respondWithSuccess($res_data, 'SUPPLIER', $request_log_id, "Notification Marked as Read.");
    }

    public function setAllNotificationAsRead(Request $request){
        $errors = [];
        $data = $request->json()->all();
        if(count($data)==0){ $data = $request->all(); }
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $notifications = Notification::where("to_source",$data['created_source'])->where("user_id",$data['created_by'])->get();

        if(count($notifications)>0){
            foreach($notifications as $notify){
                $notify->is_read = 1;
                $notify->save();
            }
        }

        return respondWithSuccess(null, 'SUPPLIER', $request_log_id, "All Notifications Marked as Read.");
    }
}
