<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\Token;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Recranet\TwigSpreadsheetBundle\Twig\Node\CellNode;

/**
 * Class CellTokenParser.
 */
class CellTokenParser extends BaseTokenParser
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
        return new CellNode($nodes, $this->getAttributes(), $lineNo, $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'xlscell';
    }
}
