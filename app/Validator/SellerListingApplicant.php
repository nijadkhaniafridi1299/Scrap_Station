<?php

namespace App\Validator;

trait SellerListingApplicant{
    protected $rules = [
        "sell_list_id"=>"required|integer",
        "buyer_id"=>"required|integer"
    ];
  
    protected $messages=[];
}
