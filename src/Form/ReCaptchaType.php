<?php declare(strict_types=1);

namespace VictorPrdh\RecaptchaBundle\Form;

use ReCaptcha\ReCaptcha;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VictorPrdh\RecaptchaBundle\Validator\Constraints\IsValidCaptcha;

class ReCaptchaType extends AbstractType
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['type'] = $options['type'];
        $view->vars['google_site_key'] = $this->parameterBag->get('victor_prdh_recaptcha.google_site_key');
    }

    public function getParent(): ?string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'recaptcha';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', 'checkbox')
            ->setDefault('mapped', false)
            ->setDefault('error_bubbling', false)
            ->setDefault('constraints', new IsValidCaptcha())
            ->setAllowedValues('type', ['checkbox', 'invisible']);
    }
}
