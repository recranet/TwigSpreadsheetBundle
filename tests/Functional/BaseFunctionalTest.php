<?php

namespace Recranet\TwigSpreadsheetBundle\Tests\Functional;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Recranet\TwigSpreadsheetBundle\Helper\Filesystem;
use Recranet\TwigSpreadsheetBundle\Tests\Functional\Fixtures\app\TestAppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BaseFunctionalTest.
 */
abstract class BaseFunctionalTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public static function setUpBeforeClass(): void
    {
        // remove temp files
        Filesystem::remove(static::getCacheDir());
        Filesystem::remove(static::getResultDir());
    }

    protected static function getCacheDir(): string
    {
        return \sprintf('%s/var/cache/%s', \dirname(__DIR__, 2), str_replace('\\', \DIRECTORY_SEPARATOR, static::class));
    }

    protected static function getResultDir(): string
    {
        return \sprintf('%s/var/result/%s', \dirname(__DIR__, 2), str_replace('\\', \DIRECTORY_SEPARATOR, static::class));
    }

    /**
     * {@inheritdoc}
     */
    protected static function getKernelClass(): string
    {
        return TestAppKernel::class;
    }

    /**
     * @param Response $response
     * @param string   $format
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     * @return Spreadsheet
     */
    protected function getDocument(Response $response, string $format = 'xlsx'): Spreadsheet
    {
        Filesystem::mkdir(static::getResultDir(), 0755);

        // create path for temp file
        $extension = strtolower($format);
        $file = tempnam(static::getResultDir(), $extension.'_');

        // save content
        Filesystem::dumpFile($file, $response->getContent());

        // load document
        return IOFactory::createReader(ucfirst($format))->load($file);
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     */
    protected function generateUrl(string $name, array $parameters = []): string
    {
        /** @var RouterInterface $router */
        $router = self::$kernel->getContainer()->get('router');

        return $router->generate($name, $parameters);
    }
}
