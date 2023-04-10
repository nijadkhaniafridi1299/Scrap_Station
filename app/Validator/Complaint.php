<?php

namespace App\Validator;

trait Complaint{

    protected $rules = [
        "complaint_type" => "required",
        "item_source" => "required",
        "item_id" => "required",
    ];

    protected $messages=[
        "complaint_type.required" => "Complaint Type is Required",
        "item_source.required" => "Item Source is Required",
        "item_id.required" => "Item Id is Required",
  ];

}