<?php declare(strict_types=1);

namespace VictorPrdh\RecaptchaBundle\Validator\Constraints;

use LogicException;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;
use function in_array;

class IsValidCaptchaValidator extends ConstraintValidator
{

    public function __construct(
        private readonly ReCaptcha $reCaptcha,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        $request = $this->requestStack->getMainRequest();
        $result = $this->reCaptcha
            ->setExpectedHostname($request->getHost())
            ->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());

        if (in_array('missing-input-response', $result->getErrorCodes())) {
            $this->context->addViolation($this->translator->trans('verify.captcha', [], 'victorprdh_recaptcha'));
        }

        if (in_array('timeout-or-duplicate', $result->getErrorCodes())) {
            $this->context->addViolation($this->translator->trans('timeout.captcha', [], 'victorprdh_recaptcha'));
        }

        if (in_array('missing-input-secret', $result->getErrorCodes())) {
            throw new LogicException($this->translator->trans('missinginput.captcha', [], 'victorprdh_recaptcha'));
        }

        if (in_array('hostname-mismatch', $result->getErrorCodes())) {
            throw new LogicException($this->translator->trans('hostname.captcha', [
                '%hostname%' => $request->getHost(),
            ], 'victorprdh_recaptcha'));
        }

        if (in_array('invalid-input-secret', $result->getErrorCodes())) {
            throw new LogicException($this->translator->trans('invalidinput.captcha', [], 'victorprdh_recaptcha'));
        }

        if (in_array('bad-request', $result->getErrorCodes())) {
            throw new LogicException($this->translator->trans('badrequest.captcha', [], 'victorprdh_recaptcha'));
        }

        if (!$result->isSuccess()) {
            $this->context->addViolation($this->translator->trans('invalid.captcha', [], 'victorprdh_recaptcha'));
        }
    }
}
