<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\Token;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Recranet\TwigSpreadsheetBundle\Twig\Node\HeaderFooterNode;
use Recranet\TwigSpreadsheetBundle\Wrapper\HeaderFooterWrapper;

/**
 * Class HeaderFooterTokenParser.
 */
class HeaderFooterTokenParser extends BaseTokenParser
{
    /**
     * @var string
     */
    private $baseType;

    /**
     * HeaderFooterTokenParser constructor.
     *
     * @param array  $attributes optional attributes for the corresponding node
     * @param string $baseType
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $attributes = [], string $baseType = HeaderFooterWrapper::BASETYPE_HEADER)
    {
        parent::__construct($attributes);

        $this->baseType = HeaderFooterWrapper::validateBaseType(strtolower($baseType));
    }

    /**
     * {@inheritdoc}
     */
    public function configureParameters(Token $token): array
    {
        return [
            'type' => [
                'type' => self::PARAMETER_TYPE_VALUE,
                'default' => new ConstantExpression(null, $token->getLine()),
            ],
            'properties' => [
                'type' => self::PARAMETER_TYPE_ARRAY,
                'default' => new ArrayExpression([], $token->getLine()),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function createNode(array $nodes = [], int $lineNo = 0): Node
    {
        return new HeaderFooterNode($nodes, $this->getAttributes(), $lineNo, $this->getTag(), $this->baseType);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'xls'.$this->baseType;
    }
}
