<?php declare(strict_types=1);

namespace VictorPrdh\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('recaptcha');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('google_site_key')->end()
            ->scalarNode('google_secret_key')->end()
            ->end();

        return $treeBuilder;
    }
}
