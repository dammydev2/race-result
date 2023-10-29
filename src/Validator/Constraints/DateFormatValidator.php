<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateFormatValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!\DateTime::createFromFormat('Y-m-d', $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}