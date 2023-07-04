<?php

namespace Services;

use Models\ValidationRule;

class ValidatorBuilder
{
    private array $rules;

    public function __construct()
    {
        $this->rules = [];
    }

    public function addRule(ValidationRule $rule): ValidatorBuilder {
        $this->rules[] = $rule;
        return $this;
    }

    public function build() : Validator {
        return new Validator($this->rules);
    }

}