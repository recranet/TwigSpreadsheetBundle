<?php

namespace Recranet\TwigSpreadsheetBundle\Tests\Twig\Pdf;

use Recranet\TwigSpreadsheetBundle\Tests\Twig\BaseTwigTest;

/**
 * Class PdfTwigTest.
 */
class PdfTwigTest extends BaseTwigTest
{
    /**
     * @return array
     */
    public function formatProvider(): array
    {
        return [['pdf']];
    }

    //
    // Tests
    //

    /**
     * @param string $format
     *
     * @throws \Exception
     *
     * @dataProvider formatProvider
     */
    public function testBasic($format): void
    {
        if (!class_exists('\\Dompdf\\Dompdf') && !class_exists('\\Mpdf\\Mpdf') && !class_exists('\\TCPDF')) {
            static::markTestSkipped('PDF rendering requires dompdf, mPDF or TCPDF');
        }

        $path = $this->render('cellProperties', $format);

        static::assertFileExists($path, 'File does not exist');
        static::assertGreaterThan(0, filesize($path), 'File is empty');
    }
}
