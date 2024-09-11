<?php

namespace Recranet\TwigSpreadsheetBundle\Tests\Functional\Fixtures\app;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestAppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Recranet\TwigSpreadsheetBundle\RecranetTwigSpreadsheetBundle(),
            new \Recranet\TwigSpreadsheetBundle\Tests\Functional\Fixtures\TestBundle\TestBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir().'/config/config.yaml');
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return dirname(__DIR__).'/../../../var/cache';
    }

    public function getLogDir(): string
    {
        return dirname(__DIR__).'/../../../var/log';
    }
}
