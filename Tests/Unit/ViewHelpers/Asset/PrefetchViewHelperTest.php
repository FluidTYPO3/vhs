<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Asset;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Class PrefetchViewHelperTest
 */
class PrefetchViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function buildReturnsMetaTag()
    {
        $instance = $this->buildViewHelperInstance(['domains' => 'test.com,test2.com', 'force' => true]);
        $instance->render();
        $result = $instance->build();
        $this->assertStringStartsWith('<meta', $result);
    }

    protected function createObjectManagerInstance(): ObjectManagerInterface
    {
        $instance = parent::createObjectManagerInstance();
        $instance->method('get')->willReturnMap(
            [
                [TagBuilder::class, new TagBuilder()],
            ]
        );
        return $instance;
    }
}
