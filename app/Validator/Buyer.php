<?php

namespace App\Validator;

trait Buyer{

    protected $rules = [
        "email" => "required_without:mobile|email|unique:buyer,email",
        "mobile" => "required_without:email|unique:buyer,mobile",
        "fullname" => "required",
        "password" => "sometimes|required",
        "iqama_cr_no" => "unique:buyer,iqama_cr_no"
    ];

    protected $messages=[
        "email.email" => "Please enter valid email id for buyer.",
        "email.unique" => "This email id is already registered for buyer.",
        "mobile.unique" => "This mobile no is already registered for buyer.",
        "fullname.required" => "Please Enter Fullname for buyer.",
        "iqama_cr_no.required" => "Please Enter Iqama No Or CR Number of buyer.",
        "iqama_cr_no.unique" => "This Iqama No Or CR Number is already registered for buyer.",
  ];

}