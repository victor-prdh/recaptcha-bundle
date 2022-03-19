<?php

namespace VictorPrdh\RecaptchaBundle\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['addSecurityHeaders', 0],
            ],
        ];
    }

    public function addSecurityHeaders(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set('X-google-site', $this->parameterBag->get('recaptcha.google_site_key'));
        $response->headers->set('X-google-secret', $this->parameterBag->get('recaptcha.google_secret_key'));
        $response->headers->set('X-TOTOR', 'lateuf');
    }
}
