<?php

namespace App\Validator;

trait Seller{

    protected $rules = [
        "email" => "required_without:mobile|email|unique:seller,email",
        "mobile" => "required_without:email|unique:seller,mobile",
        "fullname" => "required",
        "password" => "sometimes|required",
        "iqama_cr_no" => "unique:seller,iqama_cr_no"
    ];

    protected $messages=[
        "email.email" => "Please enter valid email id for seller.",
        "email.unique" => "This email id is already registered for seller.",
        "mobile.unique" => "This mobile no is already registered for seller.",
        "fullname.required" => "Please Enter Fullname. Special Characters are not allowed.",
        "iqama_cr_no.unique" => "This Iqama No Or CR Number is already registered for seller.",
  ];

}