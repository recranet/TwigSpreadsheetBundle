<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Recranet\TwigSpreadsheetBundle\Twig\Node\DrawingNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Twig\Token;

/**
 * Class DrawingTokenParser.
 */
class DrawingTokenParser extends BaseTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function configureParameters(Token $token): array
    {
        return [
            'path' => [
                'type' => self::PARAMETER_TYPE_VALUE,
                'default' => false,
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
        return new DrawingNode($nodes, $this->getAttributes(), $lineNo);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(): string
    {
        return 'xlsdrawing';
    }

    /**
     * {@inheritdoc}
     */
    public function hasBody(): bool
    {
        return false;
    }
}
