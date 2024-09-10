<?php

namespace Recranet\TwigSpreadsheetBundle\Tests;

use PHPUnit\Framework\TestCase;
use Recranet\TwigSpreadsheetBundle\DependencyInjection\RecranetTwigSpreadsheetExtension;
use Recranet\TwigSpreadsheetBundle\RecranetTwigSpreadsheetBundle;

class RecranetTwigSpreadsheetBundleTest extends TestCase
{
    public function testShouldReturnNewContainerExtension()
    {
        $testBundle = new RecranetTwigSpreadsheetBundle();

        $result = $testBundle->getContainerExtension();
        $this->assertInstanceOf(RecranetTwigSpreadsheetExtension::class, $result);
    }
}
