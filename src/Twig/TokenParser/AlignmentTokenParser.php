<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\Node\Node;
use Recranet\TwigSpreadsheetBundle\Twig\Node\AlignmentNode;
use Recranet\TwigSpreadsheetBundle\Wrapper\HeaderFooterWrapper;

/**
 * Class AlignmentTokenParser.
 */
class AlignmentTokenParser extends BaseTokenParser
{
    /**
     * @var string
     */
    private $alignment;

    /**
     * AlignmentTokenParser constructor.
     *
     * @param array  $attributes optional attributes for the corresponding node
     * @param string $alignment
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $attributes = [], string $alignment = HeaderFooterWrapper::ALIGNMENT_CENTER)
    {
        parent::__construct($attributes);

        $this->alignment = HeaderFooterWrapper::validateAlignment(strtolower($alignment));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function createNode(array $nodes = [], int $lineNo = 0): Node
    {
        return new AlignmentNode($nodes, $this->getAttributes(), $lineNo, $this->getTag(), $this->alignment);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'xls'.$this->alignment;
    }
}
