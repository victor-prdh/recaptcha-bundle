<?php

namespace VictorPrdh\RecaptchaBundle\Form;

use VictorPrdh\RecaptchaBundle\EventListener\ReCaptchaBundleValidationListener;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use ReCaptcha\ReCaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReCaptchaType.
 */
class ReCaptchaType extends AbstractType
{
    /**
     * @var ReCaptcha
     */
    private $reCaptcha;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * ReCaptchaType constructor.
     *
     * @param ReCaptcha $reCaptcha
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->reCaptcha = new ReCaptcha($this->parameterBag->get('recaptcha.google_secret_key'));
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ReCaptchaBundleValidationListener($this->reCaptcha));
    }
    
    /**
     * @inheritDoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = $options['type'];
        $view->vars['google_site_key'] = $this->parameterBag->get('recaptcha.google_site_key');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'recaptcha';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('type', 'checkbox')
            ->setDefault('mapped', false)
            ->setAllowedValues('type', ['checkbox', 'invisible']);
    }
}