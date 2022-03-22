<?php

namespace VictorPrdh\RecaptchaBundle\EventListener;

use LogicException;
use ReCaptcha\ReCaptcha;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReCaptchaBundleValidationListener implements EventSubscriberInterface
{
    private $reCaptcha;

    private $translator;

    public function __construct(ReCaptcha $reCaptcha, TranslatorInterface $translator)
    {
        $this->reCaptcha = $reCaptcha;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit'
        ];
    }

    public function onPostSubmit(FormEvent $event)
    {
        $request = Request::createFromGlobals();

        $result = $this->reCaptcha
            ->setExpectedHostname($request->getHost())
            ->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());


        if(in_array('missing-input-response', $result->getErrorCodes())) {
            $event->getForm()->addError(new FormError($this->translator->trans('verify.captcha', array(), 'victorprdh_recaptcha')));
            return;
        }

        if(in_array('invalid-input-response', $result->getErrorCodes())) {
            $event->getForm()->addError(new FormError($this->translator->trans('invalid.captcha', array(), 'victorprdh_recaptcha')));
            return;
        }

        if(in_array('timeout-or-duplicate', $result->getErrorCodes())) {
            $event->getForm()->addError(new FormError($this->translator->trans('timeout.captcha', array(), 'victorprdh_recaptcha')));
            return;
        }

        if(in_array('missing-input-secret', $result->getErrorCodes())) {
            throw new LogicException($this->translator->trans('missinginput.captcha', array(), 'victorprdh_recaptcha'));
        }

        if(in_array('hostname-mismatch', $result->getErrorCodes())) {
            throw new LogicException($this->translator->trans('hostname.captcha', array(), 'victorprdh_recaptcha'));
        }

        if(in_array('invalid-input-secret', $result->getErrorCodes())) {
            throw new LogicException($this->translator->trans('invalidinput.captcha', array(), 'victorprdh_recaptcha'));
        }

        if(in_array('bad-request', $result->getErrorCodes())) {
            throw new LogicException($this->translator->trans('badrequest.captcha', array(), 'victorprdh_recaptcha'));
        }

        if (!$result->isSuccess()) {
            $event->getForm()->addError(new FormError($this->translator->trans('invalid.captcha', array(), 'victorprdh_recaptcha')));
        }
    }
}