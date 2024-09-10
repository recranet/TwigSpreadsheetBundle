<?php

namespace Recranet\TwigSpreadsheetBundle\Tests\Functional;

/**
 * Class OdsXlsXlsxFunctionalTest.
 */
class OdsXlsXlsxFunctionalTest extends BaseFunctionalTest
{
    /**
     * @return array
     */
    public function formatProvider(): array
    {
        return [['ods'], ['xls'], ['xlsx']];
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
    public function testSimple(string $format)
    {
        $client = static::createClient();

        $url = $this->generateUrl('test_default', ['templateName' => 'simple', '_format' => $format]);
        $client->request('GET', $url);

        $document = $this->getDocument($format);
        static::assertNotNull($document, 'Document does not exist');

        $sheet = $document->getSheetByName('Test');
        static::assertNotNull($sheet, 'Sheet does not exist');

        static::assertEquals(100270, $sheet->getCell('B22')->getValue(), 'Unexpected value in B22');

        static::assertEquals('=SUM(B2:B21)', $sheet->getCell('B23')->getValue(), 'Unexpected value in B23');
        static::assertTrue($sheet->getCell('B23')->isFormula(), 'Unexpected value in isFormula');
        static::assertEquals(100270, $sheet->getCell('B23')->getCalculatedValue(), 'Unexpected calculated value in B23');
    }

    /**
     * @param string $format
     *
     * @throws \Exception
     *
     * @dataProvider formatProvider
     */
    public function testCustomResponse(string $format)
    {
        $client = static::createClient();

        $url = $this->generateUrl('test_custom_response', ['templateName' => 'simple', '_format' => $format]);
        $client->request('GET', $url);

        $response = $client->getResponse();

        static::assertNotNull($response, 'Response does not exist');
        static::assertResponseHeaderSame('Content-Disposition', 'foobar.bin');
        static::assertEquals(600, $response->getMaxAge(), 'Unexpected value in maxAge');
    }

    /**
     * @param string $format
     *
     * @throws \Exception
     *
     * @dataProvider formatProvider
     */
    public function testDocumentTemplatePath1(string $format)
    {
        $client = static::createClient();

        $url = $this->generateUrl('test_default', ['templateName' => 'documentTemplatePath1', '_format' => $format], $format);
        $client->request('GET', $url);

        $document = $this->getDocument($format);
        static::assertNotNull($document, 'Document does not exist');

        $sheet = $document->getSheet(0);
        static::assertNotNull($sheet, 'Sheet does not exist');

        static::assertEquals('Hello', $sheet->getCell('A1')->getValue(), 'Unexpected value in A1');
        static::assertEquals('World', $sheet->getCell('B1')->getValue(), 'Unexpected value in B1');
        static::assertEquals('Foo', $sheet->getCell('A2')->getValue(), 'Unexpected value in A2');
        static::assertEquals('Bar', $sheet->getCell('B2')->getValue(), 'Unexpected value in B2');
    }

    /**
     * @param string $format
     *
     * @throws \Exception
     *
     * @dataProvider formatProvider
     */
    public function testDocumentTemplatePath2(string $format)
    {
        $client = static::createClient();

        $url = $this->generateUrl('test_default', ['templateName' => 'documentTemplatePath2', '_format' => $format], $format);
        $client->request('GET', $url);

        $document = $this->getDocument($format);
        static::assertNotNull($document, 'Document does not exist');

        $sheet = $document->getSheet(0);
        static::assertNotNull($sheet, 'Sheet does not exist');

        static::assertEquals('Hello', $sheet->getCell('A1')->getValue(), 'Unexpected value in A1');
        static::assertEquals('World', $sheet->getCell('B1')->getValue(), 'Unexpected value in B1');
        static::assertEquals('Foo', $sheet->getCell('A2')->getValue(), 'Unexpected value in A2');
        static::assertEquals('Bar', $sheet->getCell('B2')->getValue(), 'Unexpected value in B2');
    }
}
