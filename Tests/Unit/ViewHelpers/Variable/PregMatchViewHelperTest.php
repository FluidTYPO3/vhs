<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class PregMatchViewHelperTest
 */
class PregMatchViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canMatchValues()
    {
        $arguments = [
            'subject' => 'foo123bar',
            'pattern' => '/[0-9]{3}/',
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertSame(1, count($test));
    }

    /**
     * @test
     */
    public function canTakeSubjectFromRenderChildren()
    {
        $arguments = [
            'pattern' => '/[0-9]{3}/',
        ];
        $test = $this->executeViewHelperUsingTagContent('foo123bar', $arguments);
        $this->assertSame(1, count($test));
    }
}
