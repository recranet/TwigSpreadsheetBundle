<?php

namespace Recranet\TwigSpreadsheetBundle\Wrapper;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use Twig\Environment;

/**
 * Class CellWrapper.
 */
class CellWrapper extends BaseWrapper
{
    protected SheetWrapper $sheetWrapper;
    protected ?Cell $object;

    /**
     * CellWrapper constructor.
     *
     * @param array        $context
     * @param Environment  $environment
     * @param SheetWrapper $sheetWrapper
     */
    public function __construct(array $context, Environment $environment, SheetWrapper $sheetWrapper)
    {
        parent::__construct($context, $environment);

        $this->sheetWrapper = $sheetWrapper;
        $this->object = null;
    }

    /**
     * @param int|null $index
     * @param array    $properties
     */
    public function start(?int $index = null, array $properties = []): void
    {
        if ($index === null) {
            $this->sheetWrapper->increaseColumn();
        } else {
            $this->sheetWrapper->setColumn($index);
        }

        if ($this->sheetWrapper->getColumn() === null || $this->sheetWrapper->getRow() === null) {
            throw new \LogicException('Column index or row number has not been set');
        }

        $this->object = $this->sheetWrapper->getObject()->getCell([
            $this->sheetWrapper->getColumn(),
            $this->sheetWrapper->getRow(),
        ]);

        $this->parameters['value'] = null;
        $this->parameters['url'] = null;

        // Remove hyperlink URL from cell properties
        if (isset($properties['url'])) {
            $this->parameters['url'] = $properties['url'];
            unset($properties['url']);
        }

        $this->parameters['properties'] = $properties;
        $this->setProperties($properties);
    }

    /**
     * @param mixed|null $value
     *
     * @throws Exception
     */
    public function value($value = null): void
    {
        if ($this->object === null) {
            throw new \LogicException('A cell must be started before writing a value.');
        }

        if ($value !== null) {
            if (isset($this->parameters['properties']['dataType'])) {
                $this->object->setValueExplicit($value, $this->parameters['properties']['dataType']);
            } else {
                $this->object->setValue($value);
            }
        }

        $this->parameters['value'] = $value;
    }

    public function hyperlinkUrl($url = null): void
    {
        if ($this->object === null) {
            throw new \LogicException('A cell must be started before setting a hyperlink.');
        }

        if ($url !== null) {
            $this->object->getHyperlink()->setUrl($url);
        } else {
            $this->object->setHyperlink(null);
        }

        $this->parameters['url'] = $url;
    }

    public function applyHyperlink(): void
    {
        if ($this->parameters['url'] !== null) {
            $this->hyperlinkUrl($this->parameters['url']);
        }
    }

    public function end(): void
    {
        if ($this->object === null) {
            throw new \LogicException('A cell must be started before ending it.');
        }

        $this->applyHyperlink();

        $this->object = null;
        $this->parameters = [];
    }

    public function getObject(): Cell
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
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function configureMappings(): array
    {
        return [
            'break' => function ($value): void {
                $this->sheetWrapper->getObject()->setBreak($this->getObject()->getCoordinate(), $value);
            },
            'dataType' => function ($value): void {
                $this->getObject()->setDataType($value);
            },
            'dataValidation' => [
                'allowBlank' => function ($value): void {
                    $this->getObject()->getDataValidation()->setAllowBlank($value);
                },
                'error' => function ($value): void {
                    $this->getObject()->getDataValidation()->setError($value);
                },
                'errorStyle' => function ($value): void {
                    $this->getObject()->getDataValidation()->setErrorStyle($value);
                },
                'errorTitle' => function ($value): void {
                    $this->getObject()->getDataValidation()->setErrorTitle($value);
                },
                'formula1' => function ($value): void {
                    $this->getObject()->getDataValidation()->setFormula1($value);
                },
                'formula2' => function ($value): void {
                    $this->getObject()->getDataValidation()->setFormula2($value);
                },
                'operator' => function ($value): void {
                    $this->getObject()->getDataValidation()->setOperator($value);
                },
                'prompt' => function ($value): void {
                    $this->getObject()->getDataValidation()->setPrompt($value);
                },
                'promptTitle' => function ($value): void {
                    $this->getObject()->getDataValidation()->setPromptTitle($value);
                },
                'showDropDown' => function ($value): void {
                    $this->getObject()->getDataValidation()->setShowDropDown($value);
                },
                'showErrorMessage' => function ($value): void {
                    $this->getObject()->getDataValidation()->setShowErrorMessage($value);
                },
                'showInputMessage' => function ($value): void {
                    $this->getObject()->getDataValidation()->setShowInputMessage($value);
                },
                'type' => function ($value): void {
                    $this->getObject()->getDataValidation()->setType($value);
                },
            ],
            'merge' => function ($value): void {
                if (\is_int($value)) {
                    $value = Coordinate::stringFromColumnIndex($value).$this->sheetWrapper->getRow();
                }
                $this->sheetWrapper->getObject()->mergeCells(\sprintf('%s:%s', $this->getObject()->getCoordinate(), $value));
            },
            'style' => function ($value): void {
                $this->sheetWrapper->getObject()->getStyle($this->getObject()->getCoordinate())->applyFromArray($value);
            },
        ];
    }
}
