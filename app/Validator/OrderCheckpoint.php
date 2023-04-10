<?php

namespace App\Validator;

trait OrderCheckpoint{
    protected $rules = [
        "order_id"=>"required|integer|min:1|exists:order,order_id",
        "chkpt_id"=>"required|integer|min:1|exists:checkpoint,chkpt_id",
        "checkin_datetime"=>"required"
    ];
  
    protected $messages=[];
}
