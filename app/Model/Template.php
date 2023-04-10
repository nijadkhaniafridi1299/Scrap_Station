<?php

namespace App\Model;

use App\Model;
use App\Message\Error;
use Illuminate\Support\Facades\Mail;
use App\Mail\Notification as MailNotification;
use App\Validator\Template as Validator;
use App\Model\NotificationErrorLog;

class Template extends Model
{
    use Validator;

    protected $primaryKey = "id";
    protected $table = "templates";
    protected $fillable = ['template_title_en','template_title_ar','subject_en','subject_ar','english','arabic','for','status','yard_id','created_source','created_by','updated_source','updated_by'];
    public $timestamps = false;

    public function add($data) {

        $data['template_title_en'] = cleanNameString($data['template_title_en']);

		if (!isset($data['template_title_en']) || $data['template_title_en'] == '') {
			Error::trigger("template.add", ["Please Enter title in English. Special Characters are not allowed."]);
			return false;
		}

        $data['template_title_ar'] = cleanNameString($data['template_title_ar']);

		if (!isset($data['template_title_ar']) || $data['template_title_ar'] == '') {
			Error::trigger("template.add", ["Please Enter title in Arabic. Special Characters are not allowed."]);
			return false;
		}

    try {
			return $template =  parent::add($data);
		}
		catch(\Exception $ex){
			Error::trigger("template.add", [$ex->getMessage()]);
			return [];
		}
    }

    public function change($data, $id, array $change_conditions = []) {
        try {
            $template = parent::change($data, $id,$change_conditions);
            return $template;
        } catch(\Exception $ex) {
            Error::trigger("template.change", [$ex->getMessage()]) ;
        }
    }

    public static function otpEmailNotification($email_id, $obj_user) {
      $search_event = "Validate Seller";
        if (!isset($email_id) || empty($email_id)) {
          //get last order
          return [
            "code" => 400,
            "message" => "Email not found, Notification can not be sent."
          ];
        }
    
        $error = '';

        $model = Template::where("template_title_en",$search_event)->first();

        $body = $model->english;

        foreach($obj_user->getAttributes() as $title => $value){
          $body = Template::replaceText($body, $title, $value);
        }

        //dump($customer->email);
        $admin_objDemo = new \stdClass();
        $admin_objDemo->subject = $model->subject_en;
        $admin_objDemo->body = $body;
        try {
          Mail::to($email_id)->send(new MailNotification($admin_objDemo));
        } catch (\Exception $e) {
          $error = $e->getMessage();

          $error_log['request']['email'] = $email_id;
          $error_log['request']['seller_id'] = $obj_user->seller_id;
          $error_log['body'] = $error;
          $error_log['f_name'] = "Push Notification";

          $errorLog = new NotificationErrorLog();
          $errorLog->add($error_log);
        }

        if (strlen($error) > 0) {
          return [
          "code" => 400,
          "message" => $error
          ];
        }
    
        return [
          "code" => 200,
          "message" => "Notifications has been sent successfully"
        ];
    }

    public static function replaceText($body, $title, $value){
      return str_replace('__'.$title.'__', $value, $body);
    }
}