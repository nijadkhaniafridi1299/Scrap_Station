<?php

namespace App\Validator;

trait Driver{

    protected $rules = [
        "email" => "required_without:mobile|email|unique:driver,email",
        "mobile" => "required_without:email|unique:driver,mobile",
        "fullname" => "required",
        "password" => "sometimes|required",
        "iqama_cr_no" => "unique:driver,iqama_cr_no"
    ];

    protected $messages=[
        "email.email" => "Please enter valid email id for driver.",
        "email.unique" => "This email id is already registered for driver.",
        "mobile.unique" => "This mobile no is already registered for driver.",
        "fullname.required" => "Please Enter Fullname. Special Characters are not allowed.",
        "iqama_cr_no.unique" => "This Iqama No Or CR Number is already registered for driver.",
  ];

}