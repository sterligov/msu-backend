<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ReCaptcha extends Constraint
{
    public $message = 'Произошла ошибка при валидации';
}