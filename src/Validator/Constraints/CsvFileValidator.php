<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CsvFileValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === null || $value->getMimeType() !== 'text/csv') {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}