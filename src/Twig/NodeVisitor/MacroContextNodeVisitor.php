<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\NodeVisitor;

use Recranet\TwigSpreadsheetBundle\Wrapper\PhpSpreadsheetWrapper;
use Twig\Environment;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\MethodCallExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\AbstractNodeVisitor;

/**
 * Class MacroContextNodeVisitor.
 */
class MacroContextNodeVisitor extends AbstractNodeVisitor
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
    protected function doEnterNode(Node $node, Environment $env): Node
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
    protected function doLeaveNode(Node $node, Environment $env): Node
    {
        return $node;
    }
}
