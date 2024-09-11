<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\NodeVisitor;

use Recranet\TwigSpreadsheetBundle\Wrapper\PhpSpreadsheetWrapper;
use Twig\Environment;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\MethodCallExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * Class MacroContextNodeVisitor.
 */
class MacroContextNodeVisitor implements NodeVisitorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node, Environment $env): Node
    {
        // add wrapper instance as argument on all method calls
        if ($node instanceof MethodCallExpression) {
            $keyNode = new ConstantExpression(PhpSpreadsheetWrapper::INSTANCE_KEY, $node->getTemplateLine());

            // add wrapper even if it not exists, we fix that later
            $valueNode = new NameExpression(PhpSpreadsheetWrapper::INSTANCE_KEY, $node->getTemplateLine());
            $valueNode->setAttribute('ignore_strict_check', true);

            /**
             * @var ArrayExpression $argumentsNode
             */
            $argumentsNode = $node->getNode('arguments');
            $argumentsNode->addElement($valueNode, $keyNode);
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node, Environment $env): Node
    {
        return $node;
    }
}
