<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\Node;

use Recranet\TwigSpreadsheetBundle\Wrapper\PhpSpreadsheetWrapper;

/**
 * Class BaseNode.
 */
abstract class BaseNode extends \Twig\Node\Node
{
    /**
     * @var string
     */
    const CODE_FIX_CONTEXT = '$context = '.PhpSpreadsheetWrapper::class.'::fixContext($context);'.PHP_EOL;

    /**
     * @var string
     */
    const CODE_INSTANCE = '$context[\''.PhpSpreadsheetWrapper::INSTANCE_KEY.'\']';

    /**
     * @return string[]
     */
    abstract public function getAllowedParents(): array;
}
