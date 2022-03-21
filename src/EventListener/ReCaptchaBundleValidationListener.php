<?php

namespace VictorPrdh\RecaptchaBundle\EventListener;

use LogicException;
use ReCaptcha\ReCaptcha;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

class ReCaptchaBundleValidationListener implements EventSubscriberInterface
{
    private $reCaptcha;

    public function __construct(ReCaptcha $reCaptcha)
    {
        $this->reCaptcha = $reCaptcha;
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
            $event->getForm()->addError(new FormError('Merci de vérifier le captcha.'));
            return;
        }

        if(in_array('invalid-input-response', $result->getErrorCodes())) {
            $event->getForm()->addError(new FormError('Le captcha n\'est pas vailde, merci de réessayer.'));
            return;
        }

        if(in_array('timeout-or-duplicate', $result->getErrorCodes())) {
            $event->getForm()->addError(new FormError('Le captcha n\'est plus vailde, merci de réessayer.'));
            return;
        }

        if(in_array('missing-input-secret', $result->getErrorCodes())) {
            throw new LogicException("Clé secrète non renseigné");
        }

        if(in_array('hostname-mismatch', $result->getErrorCodes())) {
            throw new LogicException("Nom d'hôte invalide");
        }

        if(in_array('invalid-input-secret', $result->getErrorCodes())) {
            throw new LogicException("Clé secrète invalide");
        }

        if(in_array('bad-request', $result->getErrorCodes())) {
            throw new LogicException("La demande n'a pas pu aboutir.");
        }

        if (!$result->isSuccess()) {
            $event->getForm()->addError(new FormError('Le captcha n\'est pas valide, merci de réessayer.'));
        }
    }
}