<?php

namespace Recranet\TwigSpreadsheetBundle\Tests\Twig;

/**
 * Class CsvOdsXlsXlsxErrorTwigTest.
 */
class CsvOdsXlsXlsxErrorTwigTest extends BaseTwigTest
{
    /**
     * @return array
     */
    public function formatProvider(): array
    {
        return [['csv'], ['ods'], ['xls'], ['xlsx']];
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
    public function testDocumentError($format)
    {
        $this->expectException(\Twig\Error\SyntaxError::class);
        $this->expectExceptionMessage('Node "Recranet\TwigSpreadsheetBundle\Twig\Node\DocumentNode" is not allowed inside of Node "Recranet\TwigSpreadsheetBundle\Twig\Node\SheetNode"');

        $this->renderSpreadsheet('documentError', $format);
    }

    /**
     * @param string $format
     *
     * @throws \Exception
     *
     * @dataProvider formatProvider
     */
    public function testDocumentErrorTextAfter($format)
    {
        $this->expectException(\Twig\Error\SyntaxError::class);
        $this->expectExceptionMessage('Node "Twig\Node\TextNode" is not allowed after Node "Recranet\TwigSpreadsheetBundle\Twig\Node\DocumentNode"');

        $this->renderSpreadsheet('documentErrorTextAfter', $format);
    }

    /**
     * @param string $format
     *
     * @throws \Exception
     *
     * @dataProvider formatProvider
     */
    public function testDocumentErrorTextBefore($format)
    {
        $this->expectException(\Twig\Error\SyntaxError::class);
        $this->expectExceptionMessage('Node "Twig\Node\TextNode" is not allowed before Node "Recranet\TwigSpreadsheetBundle\Twig\Node\DocumentNode"');

        $this->renderSpreadsheet('documentErrorTextBefore', $format);
    }

    /**
     * @param string $format
     *
     * @throws \Exception
     *
     * @dataProvider formatProvider
     */
    public function testSheetError($format)
    {
        $this->expectException(\Twig\Error\SyntaxError::class);
        $this->expectExceptionMessage('Node "Recranet\TwigSpreadsheetBundle\Twig\Node\RowNode" is not allowed inside of Node "Recranet\TwigSpreadsheetBundle\Twig\Node\DocumentNode"');

        $this->renderSpreadsheet('sheetError', $format);
    }
}
