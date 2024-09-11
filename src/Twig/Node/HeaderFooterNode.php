<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\Node;

use Recranet\TwigSpreadsheetBundle\Wrapper\HeaderFooterWrapper;
use Twig\Attribute\YieldReady;
use Twig\Compiler;

/**
 * Class HeaderFooterNode.
 */
#[YieldReady]
class HeaderFooterNode extends BaseNode
{
    private string $baseType;

    /**
     * HeaderFooterNode constructor.
     *
     * @param array       $nodes
     * @param array       $attributes
     * @param int         $lineNo
     * @param string      $baseType
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $nodes = [], array $attributes = [], int $lineNo = 0, string $baseType = HeaderFooterWrapper::BASETYPE_HEADER)
    {
        parent::__construct($nodes, $attributes, $lineNo);

        $this->baseType = HeaderFooterWrapper::validateBaseType(strtolower($baseType));
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this)
            ->write(self::CODE_FIX_CONTEXT)
            ->write(self::CODE_INSTANCE.'->startHeaderFooter(')
                ->repr($this->baseType)->raw(', ')
                ->subcompile($this->getNode('type'))->raw(', ')
                ->subcompile($this->getNode('properties'))
            ->raw(');'.\PHP_EOL)
            ->subcompile($this->getNode('body'))
            ->addDebugInfo($this)
            ->write(self::CODE_INSTANCE.'->endHeaderFooter();'.\PHP_EOL);
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
