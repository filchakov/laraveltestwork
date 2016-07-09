<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class OrderValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [],
        ValidatorInterface::RULE_UPDATE => [
            'product_name' => 'required|string|min:3|exists:products,name',
            'client_name' => 'required|string|min:3',
            'created_at' => 'required',
        ],
   ];
}
