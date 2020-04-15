<?php

namespace Njed\Toc\Tests;

use Njed\Toc\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            ServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(\Statamic\Extend\Manifest::class)->manifest = [
            'jedamzik/statamic-toc' => [
                'id' => 'jedamzik/statamic-toc',
                'namespace' => 'Njed\\Toc\\',
            ],
        ];
    }
}