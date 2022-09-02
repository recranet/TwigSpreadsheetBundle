<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Recranet\TwigSpreadsheetBundle\Twig\Node\SheetNode;

/**
 * Class SheetTokenParser.
 */
class SheetTokenParser extends BaseTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function configureParameters(\Twig\Token $token): array
    {
        return [
            'index' => [
                'type' => self::PARAMETER_TYPE_VALUE,
                'default' => new \Twig\Node\Expression\ConstantExpression(null, $token->getLine()),
            ],
            'properties' => [
                'type' => self::PARAMETER_TYPE_ARRAY,
                'default' => new \Twig\Node\Expression\ArrayExpression([], $token->getLine()),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createNode(array $nodes = [], int $lineNo = 0): \Twig\Node\Node
    {
        return new SheetNode($nodes, $this->getAttributes(), $lineNo, $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'xlssheet';
    }
}
