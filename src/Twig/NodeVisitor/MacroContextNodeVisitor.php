<?php

namespace Recranet\TwigSpreadsheetBundle\Twig\NodeVisitor;

use Recranet\TwigSpreadsheetBundle\Wrapper\PhpSpreadsheetWrapper;

/**
 * Class MacroContextNodeVisitor.
 */
class MacroContextNodeVisitor extends \Twig\NodeVisitor\AbstractNodeVisitor
{
    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(\Twig\Node\Node $node, \Twig\Environment $env)
    {
        // add wrapper instance as argument on all method calls
        if ($node instanceof \Twig\Node\Expression\MethodCallExpression) {
            $keyNode = new \Twig\Node\Expression\ConstantExpression(PhpSpreadsheetWrapper::INSTANCE_KEY, $node->getTemplateLine());

            // add wrapper even if it not exists, we fix that later
            $valueNode = new \Twig\Node\Expression\NameExpression(PhpSpreadsheetWrapper::INSTANCE_KEY, $node->getTemplateLine());
            $valueNode->setAttribute('ignore_strict_check', true);

            /**
             * @var \Twig\Node\Expression\ArrayExpression $argumentsNode
             */
            $argumentsNode = $node->getNode('arguments');
            $argumentsNode->addElement($valueNode, $keyNode);
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(\Twig\Node\Node $node, \Twig\Environment $env)
    {
        return $node;
    }
}
