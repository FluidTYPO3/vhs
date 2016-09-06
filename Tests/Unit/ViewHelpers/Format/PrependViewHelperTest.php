<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class PrependViewHelperTest
 */
class PrependViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canPrependValueToArgument()
    {
        $arguments = array(
            'subject' => 'before',
            'add' => 'after'
        );
        $test = $this->executeViewHelper($arguments);
        $this->assertStringStartsWith($arguments['add'], $test);
    }

    /**
     * @test
     */
    public function canPrependValueToChildContent()
    {
        $arguments = array(
            'add' => 'after'
        );
        $node = $this->createNode('Text', 'before');
        $test = $this->executeViewHelper($arguments, array(), $node);
        $this->assertStringStartsWith($arguments['add'], $test);
    }
}
