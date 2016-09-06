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
 * Class PregReplaceViewHelperTest
 */
class PregReplaceViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canReplaceValues()
    {
        $arguments = array(
            'subject' => 'foo123bar',
            'pattern' => '/[0-9]{3}/',
            'replacement' => 'baz',
        );
        $test = $this->executeViewHelper($arguments);
        $this->assertSame('foobazbar', $test);
    }

    /**
     * @test
     */
    public function canTakeSubjectFromRenderChildren()
    {
        $arguments = array(
            'pattern' => '/[0-9]{3}/',
            'replacement' => 'baz',
        );
        $test = $this->executeViewHelperUsingTagContent('Text', 'foo123bar', $arguments);
        $this->assertSame('foobazbar', $test);
    }
}
