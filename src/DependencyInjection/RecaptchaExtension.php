<?php declare(strict_types=1);

namespace VictorPrdh\RecaptchaBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class RecaptchaExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter('victor_prdh_recaptcha.' . $key, $value);
        }

        $resources = $container->getParameter('twig.form.resources');

        $container->setParameter(
            'twig.form.resources',
            [
                '@Recaptcha/form/recaptcha.html.twig',
                ...$resources,
            ],
        );
    }
}
