<?php

namespace Recranet\TwigSpreadsheetBundle\Tests\Functional\Fixtures;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class TestAppKernel.
 */
class TestAppKernel extends Kernel
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $logDir;

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Recranet\TwigSpreadsheetBundle\RecranetTwigSpreadsheetBundle(),
            new \Recranet\TwigSpreadsheetBundle\Tests\Functional\Fixtures\TestBundle\TestBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getProjectDir() . '/config/config.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectDir(): string
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return $this->logDir;
    }

    /**
     * @param string $logDir
     */
    public function setLogDir(string $logDir)
    {
        $this->logDir = $logDir;
    }
}
