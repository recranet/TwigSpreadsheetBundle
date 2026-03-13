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
     */
    public function start(?int $index = null): void
    {
        if ($index === null) {
            $this->sheetWrapper->increaseRow();
        } else {
            $this->sheetWrapper->setRow($index);
        }
    }

    public function end(): void
    {
        $this->sheetWrapper->setColumn(null);
    }
}
