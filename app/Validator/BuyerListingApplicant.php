<?php

namespace App\Validator;

trait BuyerListingApplicant{
    protected $rules = [
        "buyer_list_id"=>"required|integer",
        "seller_id"=>"required|integer"
    ];
  
    protected $messages=[];
}
