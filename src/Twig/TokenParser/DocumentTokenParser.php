<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\Token;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Recranet\TwigSpreadsheetBundle\Twig\Node\DocumentNode;

/**
 * Class DocumentTokenParser.
 */
class DocumentTokenParser extends BaseTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function configureParameters(Token $token): array
    {
        return [
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
        return new DocumentNode($nodes, $this->getAttributes(), $lineNo, $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(): string
    {
        return 'xlsdocument';
    }
}
