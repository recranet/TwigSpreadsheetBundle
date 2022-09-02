<?php

namespace Recranet\TwigSpreadsheetBundle\Twig;

use Recranet\TwigSpreadsheetBundle\Helper\Arrays;
use Recranet\TwigSpreadsheetBundle\Twig\NodeVisitor\MacroContextNodeVisitor;
use Recranet\TwigSpreadsheetBundle\Twig\NodeVisitor\SyntaxCheckNodeVisitor;
use Recranet\TwigSpreadsheetBundle\Twig\TokenParser\AlignmentTokenParser;
use Recranet\TwigSpreadsheetBundle\Twig\TokenParser\CellTokenParser;
use Recranet\TwigSpreadsheetBundle\Twig\TokenParser\DocumentTokenParser;
use Recranet\TwigSpreadsheetBundle\Twig\TokenParser\DrawingTokenParser;
use Recranet\TwigSpreadsheetBundle\Twig\TokenParser\HeaderFooterTokenParser;
use Recranet\TwigSpreadsheetBundle\Twig\TokenParser\RowTokenParser;
use Recranet\TwigSpreadsheetBundle\Twig\TokenParser\SheetTokenParser;
use Recranet\TwigSpreadsheetBundle\Wrapper\HeaderFooterWrapper;
use Recranet\TwigSpreadsheetBundle\Wrapper\PhpSpreadsheetWrapper;


/**
 * Class TwigSpreadsheetExtension.
 */
class TwigSpreadsheetExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * TwigSpreadsheetExtension constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('xlsmergestyles', [$this, 'mergeStyles']),
            new \Twig\TwigFunction('xlscellindex', [$this, 'getCurrentColumn'], ['needs_context' => true]),
            new \Twig\TwigFunction('xlsrowindex', [$this, 'getCurrentRow'], ['needs_context' => true]),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function getTokenParsers()
    {
        return [
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_CENTER),
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_LEFT),
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_RIGHT),
            new CellTokenParser(),
            new DocumentTokenParser($this->attributes),
            new DrawingTokenParser(),
            new HeaderFooterTokenParser([], HeaderFooterWrapper::BASETYPE_FOOTER),
            new HeaderFooterTokenParser([], HeaderFooterWrapper::BASETYPE_HEADER),
            new RowTokenParser(),
            new SheetTokenParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [
            new MacroContextNodeVisitor(),
            new SyntaxCheckNodeVisitor(),
        ];
    }

    /**
     * @param array $style1
     * @param array $style2
     *
     * @throws \Twig\Error\RuntimeError
     *
     * @return array
     */
    public function mergeStyles(array $style1, array $style2): array
    {
        if (!\is_array($style1) || !\is_array($style2)) {
            throw new \Twig\Error\RuntimeError('The xlsmergestyles function only works with arrays.');
        }
        return Arrays::mergeRecursive($style1, $style2);
    }

    /**
     * @param array $context
     *
     * @throws \Twig\Error\RuntimeError
     *
     * @return int|null
     */
    public function getCurrentColumn(array $context) {
        if (!isset($context[PhpSpreadsheetWrapper::INSTANCE_KEY])) {
            throw new \Twig\Error\RuntimeError('The PhpSpreadsheetWrapper instance is missing.');
        }
        return $context[PhpSpreadsheetWrapper::INSTANCE_KEY]->getCurrentColumn();
    }

    /**
     * @param array $context
     *
     * @throws \Twig\Error\RuntimeError
     *
     * @return int|null
     */
    public function getCurrentRow(array $context) {
        if (!isset($context[PhpSpreadsheetWrapper::INSTANCE_KEY])) {
            throw new \Twig\Error\RuntimeError('The PhpSpreadsheetWrapper instance is missing.');
        }
        return $context[PhpSpreadsheetWrapper::INSTANCE_KEY]->getCurrentRow();
    }
}
