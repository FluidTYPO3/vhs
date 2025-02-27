<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Context;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Condition\Context\IsCliViewHelper;
use PHPUnit\Framework\Constraint\IsType;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class IsCliViewHelperTest extends AbstractViewHelperTestCase
{
    public function testIsCliContext(): void
    {
        $instance = $this->createInstance();
        $result = IsCliViewHelper::verdict([], $this->getMockBuilder(RenderingContextInterface::class)->getMock());
        $this->assertThat($result, new IsType(IsType::TYPE_BOOL));
    }

    public function testRender(): void
    {
        $arguments = ['then' => true, 'else' => false];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(true, $result);
    }
}
