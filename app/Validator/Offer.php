<?php

namespace App\Validator;

trait Offer{
    protected $rules = [
        "seller_id"=>"required|integer",
        "buyer_id"=>"required|integer",
        "listing_source"=>"required",
        "listing_id"=>"required|integer",
    ];
  
    protected $messages=[];
}
