<?php

namespace Kodooy\StringGenerator\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Kodooy\StringGenerator\StringGeneratorServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            StringGeneratorServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'StringGenerator' => \Kodooy\StringGenerator\Facades\StringGenerator::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // Define environment setup here if needed
        $app['config']->set('string-generator.events.enabled', true);
    }
}
