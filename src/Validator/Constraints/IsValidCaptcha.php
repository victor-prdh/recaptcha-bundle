<?php declare(strict_types=1);

namespace VictorPrdh\RecaptchaBundle\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class IsValidCaptcha extends Constraint
{
    public $message = 'This value is not a valid captcha.';
}
