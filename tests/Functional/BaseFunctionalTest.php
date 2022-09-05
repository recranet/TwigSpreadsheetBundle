<?php

namespace Recranet\TwigSpreadsheetBundle\Tests\Functional;

use Recranet\TwigSpreadsheetBundle\Helper\Filesystem;
use Recranet\TwigSpreadsheetBundle\Tests\Functional\Fixtures\TestAppKernel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class BaseFunctionalTest.
 */
abstract class BaseFunctionalTest extends WebTestCase
{
    const CACHE_PATH = './../../var/cache/twig';
    const RESULT_PATH = './../../var/result';

    /**
     * @var string
     */
    protected static $ENVIRONMENT;

    /**
     * @var Client
     */
    protected static $client;

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public static function setUpBeforeClass(): void
    {
        // remove temp files
        Filesystem::remove(sprintf('%s/%s', static::CACHE_PATH, str_replace('\\', DIRECTORY_SEPARATOR, static::class)));
        Filesystem::remove(sprintf('%s/%s', static::RESULT_PATH, str_replace('\\', DIRECTORY_SEPARATOR, static::class)));
    }

    /**
     * {@inheritdoc}
     */
    protected static function getKernelClass(): string
    {
        return TestAppKernel::class;
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestAppKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->setCacheDir(sprintf('%s/../../../var/cache/%s', $kernel->getProjectDir(), str_replace('\\', DIRECTORY_SEPARATOR, static::class)));
        $kernel->setLogDir(sprintf('%s/../../../var/logs/%s', $kernel->getProjectDir(), str_replace('\\', DIRECTORY_SEPARATOR, static::class)));

        return $kernel;
    }

    /**
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function setUp(): void
    {
        static::$client = static::createClient();
    }

    /**
     * @param string $routeName
     * @param array $routeParameters
     * @param string $format
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     * @return Spreadsheet
     */
    protected function getDocument(string $routeName, array $routeParameters = [], string $format = 'xlsx'): Spreadsheet
    {
        // create document content
        $content = $this->getResponse($routeName, $routeParameters)->getContent();

        // create path for temp file
        $format = strtolower($format);
        $resultPath = sprintf('%s/%s/%s/%s.%s', __DIR__, static::RESULT_PATH, str_replace('\\', DIRECTORY_SEPARATOR, static::class), static::$ENVIRONMENT, $format);

        // save content
        Filesystem::dumpFile($resultPath, $content);

        // load document
        return IOFactory::createReader(ucfirst($format))->load($resultPath);
    }

    /**
     * @param string $routeName
     * @param array  $routeParameters
     *
     * @return Response
     */
    protected static function getResponse(string $routeName, array $routeParameters = []): Response
    {
        /**
         * @var Router $router
         */
        $router = static::$kernel->getContainer()->get('router');
        static::$client->request(Request::METHOD_GET, $router->generate($routeName, $routeParameters));

        return static::$client->getResponse();
    }
}
