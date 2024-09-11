<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\Node;

use Recranet\TwigSpreadsheetBundle\Wrapper\HeaderFooterWrapper;
use Twig\Compiler;

/**
 * Class AlignmentNode.
 */
class AlignmentNode extends BaseNode
{
    private string $alignment;

    /**
     * AlignmentNode constructor.
     *
     * @param array       $nodes
     * @param array       $attributes
     * @param int         $lineNo
     * @param string      $alignment
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $nodes = [], array $attributes = [], int $lineNo = 0, string $alignment = HeaderFooterWrapper::ALIGNMENT_CENTER)
    {
        parent::__construct($nodes, $attributes, $lineNo);

        $this->alignment = HeaderFooterWrapper::validateAlignment(strtolower($alignment));
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this)
            ->write(self::CODE_FIX_CONTEXT)
            ->write(self::CODE_INSTANCE.'->startAlignment(')
                ->repr($this->alignment)
            ->raw(');'.\PHP_EOL)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write(self::CODE_INSTANCE.'->endAlignment(trim(ob_get_clean()));'.\PHP_EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedParents(): array
    {
        return [
            HeaderFooterNode::class,
        ];
    }
}
