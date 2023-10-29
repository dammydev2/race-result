<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CsvFile extends Constraint
{
    public $message = 'The file is not a valid CSV.';
}