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
 * Class TrimViewHelperTest
 */
class TrimViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canTrimSpecificCharacters()
    {
        $arguments = array(
            'content' => 'ztrimmedy',
            'characters' => 'zy'
        );
        $test = $this->executeViewHelper($arguments);
        $this->assertSame('trimmed', $test);
    }

    /**
     * @test
     */
    public function canTrimWithArgument()
    {
        $arguments = array(
            'content' => ' trimmed '
        );
        $test = $this->executeViewHelper($arguments);
        $this->assertSame('trimmed', $test);
    }

    /**
     * @test
     */
    public function canTrimChildContent()
    {
        $arguments = array();
        $node = $this->createNode('Text', ' trimmed ');
        $test = $this->executeViewHelper($arguments, array(), $node);
        $this->assertSame('trimmed', $test);
    }
}
