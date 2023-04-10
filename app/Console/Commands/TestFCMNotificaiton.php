<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestFCMNotificaiton extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:fcm_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is to test fcm notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notification_id = "etY_7vuQ4H4hl8zTmM3hXt:APA91bHbtdpbrUNBp33rOPMe41gUoZWKLQqYLvfuf6aQhrjJkJf6QDonGL7_JITdS8xOZWK1lZM2mRNbzJPbdgEzJKcFX8y_xjw5j4ivYA5bqZ6sSkllmTNQ1ZO89Hsk6LJ9AaBXbIdE"; // web
        // $notification_id = "cCbrgg0XQvWAkfjuVb5Pk6:APA91bHF4FGVtt-rnp5jRJBXg59EKAlSAF00MR08xNNzT8pvCox72GZqyxQehUG4rIE4fb5V5va7SStAUQ4fhX6cqDW38gDqFFahZhPM1qiE1zEauAYSaCJjjJ2Sh2wuSoqpobiVxcRp"; // android
        $title = "Test Message";
        $message = "This is for zeeshan notifications";
        $type = "basic";
        $additional_data = array();
        // $additional_data["link"] = "/suppliers-manage";
        send_notification_FCM($notification_id, $title, $message, $type, "0",$additional_data); // web
        // send_notification_FCM($notification_id, $title, $message, $type, "1",$additional_data); // android
    }
}
