<?php

namespace Recranet\TwigSpreadsheetBundle\Wrapper;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\RowDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twig\Environment;

/**
 * Class SheetWrapper.
 */
class SheetWrapper extends BaseWrapper
{
    /**
     * @var int
     */
    public const COLUMN_DEFAULT = 1;

    /**
     * @var int
     */
    public const ROW_DEFAULT = 1;

    protected DocumentWrapper $documentWrapper;
    protected ?Worksheet $object;
    protected ?int $row;
    protected ?int $column;

    /**
     * SheetWrapper constructor.
     *
     * @param array           $context
     * @param Environment     $environment
     * @param DocumentWrapper $documentWrapper
     */
    public function __construct(array $context, Environment $environment, DocumentWrapper $documentWrapper)
    {
        parent::__construct($context, $environment);

        $this->documentWrapper = $documentWrapper;

        $this->object = null;
        $this->row = null;
        $this->column = null;
    }

    /**
     * @param int|string|null $index
     * @param array           $properties
     *
     * @throws Exception
     */
    public function start($index, array $properties = []): void
    {
        if (\is_int($index) && $index < $this->documentWrapper->getObject()->getSheetCount()) {
            $this->object = $this->documentWrapper->getObject()->setActiveSheetIndex($index);
        } elseif (\is_string($index)) {
            if (!$this->documentWrapper->getObject()->sheetNameExists($index)) {
                // create new sheet with a name
                $this->documentWrapper->getObject()->createSheet()->setTitle($index);
            }
            $this->object = $this->documentWrapper->getObject()->setActiveSheetIndexByName($index);
        } else {
            // create new sheet without a name
            $this->documentWrapper->getObject()->createSheet();
            $this->object = $this->documentWrapper->getObject()->setActiveSheetIndex(0);
        }

        $this->parameters['index'] = $index;
        $this->parameters['properties'] = $properties;

        $this->setProperties($properties);
    }

    public function end(): void
    {
        if ($this->object === null) {
            throw new \LogicException('A sheet must be started before ending it.');
        }

        // auto-size columns
        if (
            isset($this->parameters['properties']['columnDimension']) &&
            \is_array($this->parameters['properties']['columnDimension'])
        ) {
            /**
             * @var array $columnDimension
             */
            $columnDimension = $this->parameters['properties']['columnDimension'];
            foreach ($columnDimension as $key => $value) {
                if (isset($value['autoSize'])) {
                    if ($key === 'default') {
                        try {
                            $cellIterator = $this->object->getRowIterator()->current()->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(true);

                            foreach ($cellIterator as $cell) {
                                $this->object->getColumnDimension($cell->getColumn())->setAutoSize($value['autoSize']);
                            }
                        } catch (Exception $e) {
                            // ignore exceptions thrown when no cells are defined
                        }
                    } else {
                        $this->object->getColumnDimension($key)->setAutoSize($value['autoSize']);
                    }
                }
            }
        }

        $this->parameters = [];
        $this->object = null;
        $this->row = null;
        $this->column = null;
    }

    public function increaseRow(): void
    {
        $this->row = $this->row === null ? self::ROW_DEFAULT : $this->row + 1;
    }

    public function increaseColumn(): void
    {
        $this->column = $this->column === null ? self::COLUMN_DEFAULT : $this->column + 1;
    }

    public function getObject(): Worksheet
    {
        if ($this->object === null) {
            throw new \LogicException('Object is not initialized');
        }

        return $this->object;
    }

    public function hasObject(): bool
    {
        return $this->object !== null;
    }

    /**
     * @return int|null
     */
    public function getRow(): ?int
    {
        return $this->row;
    }

    /**
     * @param int|null $row
     */
    public function setRow($row): void
    {
        $this->row = $row;
    }

    /**
     * @return int|null
     */
    public function getColumn(): ?int
    {
        return $this->column;
    }

    /**
     * @param int|null $column
     */
    public function setColumn($column): void
    {
        $this->column = $column;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function configureMappings(): array
    {
        return [
            'autoFilter' => function ($value): void {
                $this->getObject()->setAutoFilter($value);
            },
            'columnDimension' => [
                '__multi' => fn ($index = 'default'): ColumnDimension => $index === 'default' ?
                    $this->getObject()->getDefaultColumnDimension() :
                    $this->getObject()->getColumnDimension($index),
                'autoSize' => static function ($value, ColumnDimension $object): void {
                    $object->setAutoSize($value);
                },
                'collapsed' => static function ($value, ColumnDimension $object): void {
                    $object->setCollapsed($value);
                },
                'columnIndex' => static function ($value, ColumnDimension $object): void {
                    $object->setColumnIndex($value);
                },
                'outlineLevel' => static function ($value, ColumnDimension $object): void {
                    $object->setOutlineLevel($value);
                },
                'visible' => static function ($value, ColumnDimension $object): void {
                    $object->setVisible($value);
                },
                'width' => static function ($value, ColumnDimension $object): void {
                    $object->setWidth($value);
                },
                'xfIndex' => static function ($value, ColumnDimension $object): void {
                    $object->setXfIndex($value);
                },
            ],
            'pageMargins' => [
                'top' => function ($value): void {
                    $this->getObject()->getPageMargins()->setTop($value);
                },
                'bottom' => function ($value): void {
                    $this->getObject()->getPageMargins()->setBottom($value);
                },
                'left' => function ($value): void {
                    $this->getObject()->getPageMargins()->setLeft($value);
                },
                'right' => function ($value): void {
                    $this->getObject()->getPageMargins()->setRight($value);
                },
                'header' => function ($value): void {
                    $this->getObject()->getPageMargins()->setHeader($value);
                },
                'footer' => function ($value): void {
                    $this->getObject()->getPageMargins()->setFooter($value);
                },
            ],
            'pageSetup' => [
                'fitToHeight' => function ($value): void {
                    $this->getObject()->getPageSetup()->setFitToHeight($value);
                },
                'fitToPage' => function ($value): void {
                    $this->getObject()->getPageSetup()->setFitToPage($value);
                },
                'fitToWidth' => function ($value): void {
                    $this->getObject()->getPageSetup()->setFitToWidth($value);
                },
                'horizontalCentered' => function ($value): void {
                    $this->getObject()->getPageSetup()->setHorizontalCentered($value);
                },
                'orientation' => function ($value): void {
                    $this->getObject()->getPageSetup()->setOrientation($value);
                },
                'paperSize' => function ($value): void {
                    $this->getObject()->getPageSetup()->setPaperSize($value);
                },
                'printArea' => function ($value): void {
                    $this->getObject()->getPageSetup()->setPrintArea($value);
                },
                'scale' => function ($value): void {
                    $this->getObject()->getPageSetup()->setScale($value);
                },
                'verticalCentered' => function ($value): void {
                    $this->getObject()->getPageSetup()->setVerticalCentered($value);
                },
            ],
            'printGridlines' => function ($value): void {
                $this->getObject()->setPrintGridlines($value);
            },
            'protection' => [
                'autoFilter' => function ($value): void {
                    $this->getObject()->getProtection()->setAutoFilter($value);
                },
                'deleteColumns' => function ($value): void {
                    $this->getObject()->getProtection()->setDeleteColumns($value);
                },
                'deleteRows' => function ($value): void {
                    $this->getObject()->getProtection()->setDeleteRows($value);
                },
                'formatCells' => function ($value): void {
                    $this->getObject()->getProtection()->setFormatCells($value);
                },
                'formatColumns' => function ($value): void {
                    $this->getObject()->getProtection()->setFormatColumns($value);
                },
                'formatRows' => function ($value): void {
                    $this->getObject()->getProtection()->setFormatRows($value);
                },
                'insertColumns' => function ($value): void {
                    $this->getObject()->getProtection()->setInsertColumns($value);
                },
                'insertHyperlinks' => function ($value): void {
                    $this->getObject()->getProtection()->setInsertHyperlinks($value);
                },
                'insertRows' => function ($value): void {
                    $this->getObject()->getProtection()->setInsertRows($value);
                },
                'objects' => function ($value): void {
                    $this->getObject()->getProtection()->setObjects($value);
                },
                'password' => function ($value): void {
                    $this->getObject()->getProtection()->setPassword($value);
                },
                'pivotTables' => function ($value): void {
                    $this->getObject()->getProtection()->setPivotTables($value);
                },
                'scenarios' => function ($value): void {
                    $this->getObject()->getProtection()->setScenarios($value);
                },
                'selectLockedCells' => function ($value): void {
                    $this->getObject()->getProtection()->setSelectLockedCells($value);
                },
                'selectUnlockedCells' => function ($value): void {
                    $this->getObject()->getProtection()->setSelectUnlockedCells($value);
                },
                'sheet' => function ($value): void {
                    $this->getObject()->getProtection()->setSheet($value);
                },
                'sort' => function ($value): void {
                    $this->getObject()->getProtection()->setSort($value);
                },
            ],
            'rightToLeft' => function ($value): void {
                $this->getObject()->setRightToLeft($value);
            },
            'rowDimension' => [
                '__multi' => fn ($index = 'default'): RowDimension => $index === 'default' ?
                    $this->getObject()->getDefaultRowDimension() :
                    $this->getObject()->getRowDimension($index),
                'collapsed' => static function ($value, RowDimension $object): void {
                    $object->setCollapsed($value);
                },
                'outlineLevel' => static function ($value, RowDimension $object): void {
                    $object->setOutlineLevel($value);
                },
                'rowHeight' => static function ($value, RowDimension $object): void {
                    $object->setRowHeight($value);
                },
                'rowIndex' => static function ($value, RowDimension $object): void {
                    $object->setRowIndex($value);
                },
                'visible' => static function ($value, RowDimension $object): void {
                    $object->setVisible($value);
                },
                'xfIndex' => static function ($value, RowDimension $object): void {
                    $object->setXfIndex($value);
                },
                'zeroHeight' => static function ($value, RowDimension $object): void {
                    $object->setZeroHeight($value);
                },
            ],
            'sheetState' => function ($value): void {
                $this->getObject()->setSheetState($value);
            },
            'showGridlines' => function ($value): void {
                $this->getObject()->setShowGridlines($value);
            },
            'tabColor' => function ($value): void {
                $this->getObject()->getTabColor()->setRGB($value);
            },
            'zoomScale' => function ($value): void {
                $this->getObject()->getSheetView()->setZoomScale($value);
            },
            'freezePane' => function ($value): void {
                $this->getObject()->freezePane($value);
            },
        ];
    }
}
