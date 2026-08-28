<?php

declare(strict_types=1);

namespace VictorPrdh\RecaptchaBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use VictorPrdh\RecaptchaBundle\DependencyInjection\Configuration;

final class ConfigurationTest extends TestCase
{
    public function testDefaultConfigurationIsEmpty(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), []);

        self::assertSame([], $config);
    }

    public function testConfigurationAcceptsSiteAndSecretKeys(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [
            'recaptcha' => [
                'google_site_key' => 'site-key',
                'google_secret_key' => 'secret-key',
            ],
        ]);

        self::assertSame('site-key', $config['google_site_key']);
        self::assertSame('secret-key', $config['google_secret_key']);
    }
}
