<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class ProductValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name' => 'string',
            'price' => 'integer',
            'currency' => 'string',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'name' => 'string',
            'price' => 'integer',
            'currency' => 'string',
        ]
    ];
}
