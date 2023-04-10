<?php

namespace App\Validator;

trait Comment{

    protected $rules = [
        "comment_text" =>"required",
        "comment_type" =>"required"
    ];
    protected $messages=[
        "comment_text.required" => "Comment text is required",
        "comment_type.required" => "Comment Type is required.",
    ];
}


