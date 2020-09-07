<?php


namespace App\Validator\Constraints;

use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
final class ReCaptchaValidator extends ConstraintValidator
{
    private ?Request $request = null;

    private ReCaptcha $reCaptcha;

    public function __construct(RequestStack $requestStack, ReCaptcha $reCaptcha)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->reCaptcha = $reCaptcha;
    }

    public function validate($value, Constraint $constraint): void
    {
        $response = $this->reCaptcha->verify($value, $this->request->getClientIp());

        if (!$response->isSuccess()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}