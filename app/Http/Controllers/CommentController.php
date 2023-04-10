<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Comment;
use App\Model\Order;
// use App\Model\Complaint;
use App\Model\Notification;
use App\Model\User;
use App\Model\Buyer;
use App\Message\Error;
use Auth;
class CommentController extends Controller
{
    public function getCommentOfType(Request $request, $ref_type, $ref_id){
        $errors = [];
    //    dd(Auth::user()->user_id);
        if($ref_type == null){
            Error::trigger("comment.reftype", ["No Reference Type provided in API."]);
            array_push($errors, \App\Message\Error::get('comment.reftype'));
        }

        if($ref_id == null){
            Error::trigger("comment.refid", ["No Reference Id provided in API."]);
            array_push($errors, \App\Message\Error::get('comment.refid'));
        }

        $data = $request->json()->all();
        if (count($data) == 0) { $data = $request->all(); }

        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        if (isset($errors) && count($errors) > 0) {
            return respondWithError($errors,$request_log_id);
        }

        $res_data = array();
        $comments = Comment::with('images')->where("ref_type",$ref_type)->where("ref_id",$ref_id)->where("status",1)->where("deleted_at",null)
        ->select('comment_id', 'ref_type', 'ref_id', 'comment_text', 'comment_type', 'created_source', 'created_by', 'created_at');
        if($data['created_source']=="seller"){ $comments = $comments->where("comment_type","public"); }
        $comments = $comments->orderBy("created_at","asc")->get();
        $res_data['comments'] = array();
        if(count($comments)>0){
            foreach($comments as $comment){
                $comment->images;
                $comment_array = json_decode(json_encode($comment),true);
                $commenter = $comment->createdByPerson->fullname;
                $comment_array['commenter'] = $commenter;
                array_push($res_data['comments'],$comment_array);
            }
        }
        return respondWithSuccess($res_data, 'COMMENT', $request_log_id, "");
    }
    
    public function addComment(Request $request){
        $errors = [];
        $data = $request->json()->all();
        if (count($data) == 0) { $data = $request->all(); }
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        $comment = new Comment();
        
        $data['comment']['created_source'] =  $data['created_source'];
        $data['comment']['created_by'] =  $data['created_by'];
        $data['comment']['created_at'] =  date('Y-m-d H:i:s');

        if(count($data['images'])>0 && empty($data['comment']['comment_text'])){ $data['comment']['comment_text'] = "."; }

        $validated_comment = $comment->validateAtStart($data['comment']);

        if (!$validated_comment ) { array_push($errors, \App\Message\Error::get('comment.start')); }
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        $comment = $comment->add($data['comment']);

        if (!is_object($comment)) {
            $errors = \App\Message\Error::get('comment.add');
        }

        if (isset($errors) && count($errors) > 0) {
            return respondWithError($errors,$request_log_id);
        }

        if(isset($data['images']) && count($data['images'])>0){
            foreach($data['images'] as $img_path){
                $img_data = prepareImageData("comment",$img_path,$data,"Comment Image");
                $comment->images()->create($img_data);
            }
        }

        $seller_fcm_token = null;
        $seller_id = null;
        
        $ref_type = $data['comment']['ref_type'];
        $ref_id = $data['comment']['ref_id'];
        $ref_no = "";

        switch($ref_type){
            case "order":
                $order = Order::find($ref_id);
                $seller_id = $order->offer->seller_id;
                $ref_no = $order->order_no;
                $seller_fcm_token = $order->offer->seller->fcm_token_for_seller_app;
            break;
            // case "complaint": break;
        }

        
        $title = "Comment Added";
        $type = "basic";
        $created_source = $data['created_source'];

        $message = "Comment added by ".$created_source." on [".$ref_type." # ".$ref_no."]" ;
/*
        if($created_source == "user" || $created_source == "buyer"){
            $is_sent = 0;
            if ($seller_fcm_token != "" || $seller_fcm_token != null) {
                $notification_id = $seller_fcm_token;
                $source=1;
                $res = send_notification_FCM($notification_id, $title, $message, $type,$source);
                $is_sent = 1;
            } 
            $insertNotification = Notification::insert([
                'to_source' => "seller",
                'user_id' => $seller_id,
                'reference_type' => 'ticket_comment',
                'reference_id' => $ref_id,
                'notification_body' => $message,
                'is_sent' => $is_sent,
                'created_source' => $data['created_source'],
                'created_by' => $data['created_by'],
            ]);
        }
        if($created_source == "seller" || $created_source == "buyer"){
            $getUsers = User::join('groups','users.group_id','=','groups.group_id')
            ->where('groups.role_key','YARD_ADMIN')->get()->toArray();
            $save_Notification = array();
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
                    'reference_type' => "ticket_comment",
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
*/
        $res_data = array();
        $res_data['comment'] = $comment;
        $res_data['comment']['commenter'] = $comment->createdByPerson->fullname;

        return respondWithSuccess($res_data, 'COMMENT', $request_log_id, "Comment added successfully!", 201);
    }

    public function deleteComment(Request $request, $comment_id){
        $errors = [];
        $data = $request->json()->all();
        if (count($data) == 0) { $data = $request->all(); }
        $request_log_id = $data['request_log_id'];
        unset($data['request_log_id']);

        if($comment_id == null){
            array_push($errors, \App\Message\Error::get('comment.remove'));
        }

        $comment = Comment::find($comment_id);

        if (!is_object($comment)) {
            Error::trigger("comment.remove", ["No Comment present against this id."]);
            array_push($errors, \App\Message\Error::get('comment.remove'));
        }

        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id); }

        if(($comment->created_source != $data['created_source']) || ($comment->created_by != $data['created_by'])){
            Error::trigger("comment.remove", ["This is not your comment. You cannot delete it"]);
            array_push($errors, \App\Message\Error::get('comment.remove'));
        }
        
        if (isset($errors) && count($errors) > 0) { return respondWithError($errors,$request_log_id,204); }
        
        $comment->remove($comment_id);
        if (isset($errors) && count($errors) > 0) {
            return respondWithError($errors,$request_log_id,404);
        }

        $res_data = null;

        return respondWithSuccess($res_data, 'COMMENT', $request_log_id, "Comment Deleted successfully!", 200);
    }
}
