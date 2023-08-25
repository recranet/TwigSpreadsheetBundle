<?php

namespace Recranet\TwigSpreadsheetBundle\Wrapper;

use Twig\Environment;

/**
 * Class SheetWrapper.
 */
class RowWrapper extends BaseWrapper
{
    protected SheetWrapper $sheetWrapper;

    /**
     * RowWrapper constructor.
     *
     * @param array        $context
     * @param Environment  $environment
     * @param SheetWrapper $sheetWrapper
     */
    public function __construct(array $context, Environment $environment, SheetWrapper $sheetWrapper)
    {
        parent::__construct($context, $environment);

        $this->sheetWrapper = $sheetWrapper;
    }

    /**
     * @param int|null $index
     *
     * @throws \LogicException
     */
    public function start(int $index = null): void
    {
        if ($this->sheetWrapper->getObject() === null) {
            throw new \LogicException();
        }

        if ($index === null) {
            $this->sheetWrapper->increaseRow();
        } else {
            $this->sheetWrapper->setRow($index);
        }
    }

    /**
     * @throws \LogicException
     */
    public function end(): void
    {
        if ($this->sheetWrapper->getObject() === null) {
            throw new \LogicException();
        }

        $this->sheetWrapper->setColumn(null);
    }
}
