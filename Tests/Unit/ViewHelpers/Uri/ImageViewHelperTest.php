<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class ImageViewHelperTest
 */
class ImageViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function callsExpectedMethodSequence()
    {
        $mock = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['preProcessImage'])->getMock();
        $mock->setArguments(['src' => 'foobar']);
        $this->assertStringStartsWith('http://', $mock->render());
    }
}
