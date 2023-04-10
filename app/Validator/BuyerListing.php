<?php

namespace App\Validator;

trait BuyerListing{
    protected $rules = [
        "buyer_id" => "required|integer",
        "mat_id" => "required|integer",
        "quantity" => "required|numeric",
        "expected_price_per_unit" => "required|numeric",
    ];
  
    protected $messages=[];
}
