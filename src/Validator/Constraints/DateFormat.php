<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateFormat extends Constraint
{
    public $message = "The date is not in the format YYYY-MM-DD.";
}