<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class ClientValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name' => 'required|min:5',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'name' => 'required|min:5',
        ],
    ];
}
