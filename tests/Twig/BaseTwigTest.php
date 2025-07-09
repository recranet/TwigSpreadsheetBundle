<?php

namespace Recranet\TwigSpreadsheetBundle\Tests\Twig;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;
use Recranet\TwigSpreadsheetBundle\Helper\Filesystem;
use Recranet\TwigSpreadsheetBundle\Twig\TwigSpreadsheetExtension;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class BaseTwigTest.
 */
abstract class BaseTwigTest extends TestCase
{
    /**
     * @var \Twig\Environment
     */
    protected static $environment;

    private static function getCacheDir(): string
    {
        return sprintf('%s/var/cache/%s', dirname(dirname(__DIR__)) , str_replace('\\', \DIRECTORY_SEPARATOR, static::class));
    }

    private static function getResultDir(): string
    {
        return sprintf('%s/var/result/%s', dirname(dirname(__DIR__)) , str_replace('\\', \DIRECTORY_SEPARATOR, static::class));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Twig\Error\LoaderError
     */
    public static function setUpBeforeClass(): void
    {
        // remove temp files
        Filesystem::remove(static::getCacheDir());
        Filesystem::remove(static::getResultDir());

        // set up Twig environment
        $twigFileSystem = new \Twig\Loader\FilesystemLoader([
            __DIR__.'/Fixtures/views',
        ]);

        $twigFileSystem->addPath(__DIR__.'/Fixtures/templates', 'templates');

        static::$environment = new \Twig\Environment($twigFileSystem, ['debug' => true, 'strict_variables' => true]);
        static::$environment->addExtension(new TwigSpreadsheetExtension([
            'pre_calculate_formulas' => true,
            'cache' => [
                'bitmap' => self::getCacheDir().'/spreadsheet/bitmap',
                'xml' => self::getCacheDir().'/spreadsheet/xml',
            ],
            'csv_writer' => [
                'delimiter' => ',',
                'enclosure' => '"',
                'excel_compatibility' => false,
                'include_separator_line' => false,
                'line_ending' => \PHP_EOL,
                'sheet_index' => 0,
                'use_bom' => true,
            ],
        ]));
        static::$environment->addGlobal('assetsDir', __DIR__.'/Fixtures/assets');
    }

    /**
     * @param string $templateName
     * @param string $format
     *
     * @return string
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Twig\Error\Error
     */
    protected function render(string $templateName, string $format = 'xlsx'): string
    {
        $format = strtolower($format);

        // prepare global variables
        $request = new Request();
        $request->setRequestFormat($format);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $appVariable = new AppVariable();
        $appVariable->setRequestStack($requestStack);

        $output = static::$environment->load($templateName.'.twig')->render(['app' => $appVariable]);

        Filesystem::mkdir(static::getResultDir(), 0755);

        // create path for temp file
        $extension = strtolower($format);
        $file = tempnam(static::getResultDir(), $extension.'_');

        // save content
        Filesystem::dumpFile($file, $output);

        return $file;
    }

    /**
     * @param string $templateName
     * @param string $format
     *
     * @return Spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Twig\Error\Error
     */
    protected function renderSpreadsheet(string $templateName, string $format = 'xlsx'): Spreadsheet
    {
        $file = $this->render($templateName, $format);

        // load document
        return IOFactory::createReader(ucfirst($format))->load($file);
    }
}
