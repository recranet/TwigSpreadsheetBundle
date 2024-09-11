<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Recranet\TwigSpreadsheetBundle\Twig\Node\SheetNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\Token;

/**
 * Class SheetTokenParser.
 */
class SheetTokenParser extends BaseTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function configureParameters(Token $token): array
    {
        return [
            'index' => [
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
     */
    public function createNode(array $nodes = [], int $lineNo = 0): Node
    {
        return new SheetNode($nodes, $this->getAttributes(), $lineNo);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(): string
    {
        return 'xlssheet';
    }
}
