<?php

declare(strict_types=1);

namespace VictorPrdh\RecaptchaBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use VictorPrdh\RecaptchaBundle\Validator\Constraints\IsValidCaptcha;

final class IsValidCaptchaTest extends TestCase
{
    public function testDefaultMessage(): void
    {
        $constraint = new IsValidCaptcha();

        self::assertSame('This value is not a valid captcha.', $constraint->message);
    }
}
