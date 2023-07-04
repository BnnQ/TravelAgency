<?php

namespace Services;

use Exceptions\ValueInvalidationException;
use Models\ValidationRule;

class Validator
{

    /**
     * @param ValidationRule[] $rules
     */
    public function __construct(private readonly array $rules)
    {
        //empty
    }


    /**
     * Validates softly and returns error message if validation error occurred, null otherwise.
     * @return string|null a validation error message if it occurred, null otherwise.
     */
    public function validateSoft() : string|null {
        foreach ($this->rules as $rule) {
            if (!$rule->validationExpression)
                return $rule->invalidationErrorMessage;
        }

        return null;
    }

    /**
     * Validates strictly and throws an exception if validation error occurred.
     * @throws ValueInvalidationException
     */
    public function validateHard() : void {
        foreach ($this->rules as $rule) {
            if (!$rule->validationExpression)
                throw new ValueInvalidationException($rule->invalidationErrorMessage);
        }
    }

}