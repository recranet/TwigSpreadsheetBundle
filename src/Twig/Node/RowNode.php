<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\Node;

/**
 * Class RowNode.
 */
class RowNode extends BaseNode
{
    /**
     * @param \Twig\Compiler $compiler
     */
    public function compile(\Twig\Compiler $compiler)
    {
        $compiler->addDebugInfo($this)
            ->write(self::CODE_FIX_CONTEXT)
            ->write(self::CODE_INSTANCE.'->startRow(')
                ->subcompile($this->getNode('index'))
            ->raw(');'.PHP_EOL)
            ->subcompile($this->getNode('body'))
            ->addDebugInfo($this)
            ->write(self::CODE_INSTANCE.'->endRow();'.PHP_EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedParents(): array
    {
        return [
            SheetNode::class,
        ];
    }
}
