<?php

namespace Recranet\TwigSpreadsheetBundle\Wrapper;

use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Twig\Environment;

/**
 * Class PhpSpreadsheetWrapper.
 */
class PhpSpreadsheetWrapper
{
    /**
     * @var string
     */
    public const INSTANCE_KEY = '_tsb';

    private DocumentWrapper $documentWrapper;
    private SheetWrapper $sheetWrapper;
    private RowWrapper $rowWrapper;
    private CellWrapper $cellWrapper;
    private HeaderFooterWrapper $headerFooterWrapper;
    private DrawingWrapper $drawingWrapper;

    /**
     * PhpSpreadsheetWrapper constructor.
     *
     * @param array       $context
     * @param Environment $environment
     * @param array       $attributes
     */
    public function __construct(array $context, Environment $environment, array $attributes = [])
    {
        $this->documentWrapper = new DocumentWrapper($context, $environment, $attributes);
        $this->sheetWrapper = new SheetWrapper($context, $environment, $this->documentWrapper);
        $this->rowWrapper = new RowWrapper($context, $environment, $this->sheetWrapper);
        $this->cellWrapper = new CellWrapper($context, $environment, $this->sheetWrapper);
        $this->headerFooterWrapper = new HeaderFooterWrapper($context, $environment, $this->sheetWrapper);
        $this->drawingWrapper = new DrawingWrapper($context, $environment, $this->sheetWrapper, $this->headerFooterWrapper, $attributes);
    }

    /**
     * @return int|null
     */
    public function getCurrentColumn(): ?int
    {
        return $this->sheetWrapper->getColumn();
    }

    /**
     * @return int|null
     */
    public function getCurrentRow(): ?int
    {
        return $this->sheetWrapper->getRow();
    }

    /**
     * @param array $properties
     *
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \RuntimeException
     */
    public function startDocument(array $properties = []): void
    {
        $this->documentWrapper->start($properties);
    }

    /**
     * @return \Generator<string>
     *
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws IOException
     */
    public function yieldDocument(): \Generator
    {
        yield $this->documentWrapper->write();
    }

    /**
     * @throws \LogicException
     */
    public function endDocument(): void
    {
        $this->documentWrapper->end();
    }

    /**
     * @param int|string|null $index
     * @param array           $properties
     *
     * @throws \LogicException
     * @throws Exception
     * @throws \RuntimeException
     */
    public function startSheet($index = null, array $properties = []): void
    {
        $this->sheetWrapper->start($index, $properties);
    }

    /**
     * @throws \LogicException
     * @throws \Exception
     */
    public function endSheet(): void
    {
        $this->sheetWrapper->end();
    }

    /**
     * @param int|null $index
     *
     * @throws \LogicException
     */
    public function startRow(int $index = null): void
    {
        $this->rowWrapper->start($index);
    }

    /**
     * @throws \LogicException
     */
    public function endRow(): void
    {
        $this->rowWrapper->end();
    }

    /**
     * @param int|null $index
     * @param array    $properties
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function startCell(int $index = null, array $properties = []): void
    {
        $this->cellWrapper->start($index, $properties);
    }

    /**
     * @param mixed|null $value
     *
     * @throws Exception
     */
    public function setCellValue($value = null): void
    {
        $this->cellWrapper->value($value);
    }

    public function endCell(): void
    {
        $this->cellWrapper->end();
    }

    /**
     * @param string      $baseType
     * @param string|null $type
     * @param array       $properties
     *
     * @throws \LogicException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function startHeaderFooter(string $baseType, string $type = null, array $properties = []): void
    {
        $this->headerFooterWrapper->start($baseType, $type, $properties);
    }

    /**
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function endHeaderFooter(): void
    {
        $this->headerFooterWrapper->end();
    }

    /**
     * @param string|null $type
     * @param array       $properties
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function startAlignment(string $type = null, array $properties = []): void
    {
        $this->headerFooterWrapper->startAlignment($type, $properties);
    }

    /**
     * @param string|null $value
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function endAlignment(string $value = null): void
    {
        $this->headerFooterWrapper->endAlignment($value);
    }

    /**
     * @param string $path
     * @param array  $properties
     *
     * @throws IOException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \RuntimeException
     * @throws Exception
     */
    public function startDrawing(string $path, array $properties = []): void
    {
        $this->drawingWrapper->start($path, $properties);
    }

    public function endDrawing(): void
    {
        $this->drawingWrapper->end();
    }

    /**
     * Copies the PhpSpreadsheetWrapper instance from 'varargs' to '_tsb'. This is necessary for all Twig code running
     * in sub-functions (e.g. block, macro, ...) since the root context is lost. To fix the sub-context a reference to
     * the PhpSpreadsheetWrapper instance is included in all function calls.
     *
     * @param array $context
     *
     * @return array
     */
    public static function fixContext(array $context): array
    {
        if (!isset($context[self::INSTANCE_KEY]) && isset($context['varargs']) && \is_array($context['varargs'])) {
            foreach ($context['varargs'] as $arg) {
                if ($arg instanceof self) {
                    $context[self::INSTANCE_KEY] = $arg;
                    break;
                }
            }
        }

        return $context;
    }
}
