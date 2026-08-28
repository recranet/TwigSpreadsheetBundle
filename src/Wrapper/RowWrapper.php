<?php

namespace Recranet\TwigSpreadsheetBundle\Wrapper;

use Twig\Environment;

/**
 * Class SheetWrapper.
 */
class RowWrapper extends BaseWrapper
{
    /**
     * RowWrapper constructor.
     *
     * @param array        $context
     * @param Environment  $environment
     * @param SheetWrapper $sheetWrapper
     */
    public function __construct(
        array $context,
        Environment $environment,
        protected SheetWrapper $sheetWrapper,
    ) {
        parent::__construct($context, $environment);
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
