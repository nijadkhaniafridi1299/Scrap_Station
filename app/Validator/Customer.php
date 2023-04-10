<?php

namespace App\Validator;

trait Customer{

    protected $rules = [
        "email" =>"sometimes|required|email|unique:customers",
        "fullname" => "required",
        "password" => "sometimes|required"
    ];

}