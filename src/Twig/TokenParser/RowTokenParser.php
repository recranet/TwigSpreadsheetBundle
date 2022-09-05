<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\Token;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Recranet\TwigSpreadsheetBundle\Twig\Node\RowNode;

/**
 * Class RowTokenParser.
 */
class RowTokenParser extends BaseTokenParser
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createNode(array $nodes = [], int $lineNo = 0): Node
    {
        return new RowNode($nodes, $this->getAttributes(), $lineNo, $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(): string
    {
        return 'xlsrow';
    }
}
