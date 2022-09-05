<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\Node\Node;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ArrayExpression;

/**
 * Class BaseTokenParser.
 */
abstract class BaseTokenParser extends AbstractTokenParser
{
    /**
     * @var int
     */
    public const PARAMETER_TYPE_ARRAY = 0;

    /**
     * @var int
     */
    public const PARAMETER_TYPE_VALUE = 1;

    private array $attributes;

    /**
     * BaseTokenParser constructor.
     *
     * @param array $attributes optional attributes for the corresponding node
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @param Token $token
     *
     * @return array
     */
    public function configureParameters(Token $token): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Create a concrete node.
     *
     * @param array $nodes
     * @param int   $lineNo
     *
     * @return Node
     */
    abstract public function createNode(array $nodes = [], int $lineNo = 0): Node;

    /**
     * @return bool
     */
    public function hasBody(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function parse(Token $token)
    {
        // parse parameters
        $nodes = $this->parseParameters($this->configureParameters($token));

        // parse body
        if ($this->hasBody()) {
            $nodes['body'] = $this->parseBody();
        }

        return $this->createNode($nodes, $token->getLine());
    }

    /**
     * @param array $parameterConfiguration
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws SyntaxError
     *
     * @return AbstractExpression[]
     */
    private function parseParameters(array $parameterConfiguration = []): array
    {
        // parse expressions
        $expressions = [];
        while (!$this->parser->getStream()->test(Token::BLOCK_END_TYPE)) {
            $expressions[] = $this->parser->getExpressionParser()->parseExpression();
        }

        // end of expressions
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        // map expressions to parameters
        $parameters = [];
        foreach ($parameterConfiguration as $parameterName => $parameterOptions) {
            // try mapping expression
            $expression = reset($expressions);
            if ($expression !== false) {
                switch ($parameterOptions['type']) {
                    case self::PARAMETER_TYPE_ARRAY:
                        // check if expression is valid array
                        $valid = $expression instanceof ArrayExpression;
                        break;
                    case self::PARAMETER_TYPE_VALUE:
                        // check if expression is valid value
                        $valid = !($expression instanceof ArrayExpression);
                        break;
                    default:
                        throw new \InvalidArgumentException('Invalid parameter type');
                }

                if ($valid) {
                    // set expression as parameter and remove it from expressions list
                    $parameters[$parameterName] = array_shift($expressions);
                    continue;
                }
            }

            // set default as parameter otherwise or throw exception if default is false
            if ($parameterOptions['default'] === false) {
                throw new SyntaxError('A required parameter is missing');
            }
            $parameters[$parameterName] = $parameterOptions['default'];
        }

        if (\count($expressions) > 0) {
            throw new SyntaxError('Too many parameters');
        }

        return $parameters;
    }

    /**
     * @return Node
     * @throws SyntaxError
     */
    private function parseBody(): Node
    {
        // parse till matching end tag is found
        $body = $this->parser->subparse(fn(Token $token) => $token->test('end'.$this->getTag()), true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        return $body;
    }
}
