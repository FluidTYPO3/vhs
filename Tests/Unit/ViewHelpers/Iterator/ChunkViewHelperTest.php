<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class ChunkViewHelperTest
 */
class ChunkViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsConfiguredItemNumberIfFixed()
    {
        $arguments = array(
            'count' => 5,
            'fixed' => true,
            'subject' => array('a', 'b', 'c', 'd', 'e'),
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(5, $result);
    }

    /**
     * @test
     */
    public function returnsConfiguredItemNumberIfFixedAndSubjectIsEmpty()
    {
        $arguments = array(
            'count' => 5,
            'fixed' => true,
            'subject' => array(),
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(5, $result);
    }

    /**
     * @test
     */
    public function returnsExpectedItemNumberIfNotFixed()
    {
        $arguments = array(
            'count' => 4,
            'subject' => array('a', 'b', 'c', 'd', 'e'),
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(2, $result);
    }

    /**
     * @test
     */
    public function returnsEmptyResultForEmptySubjectAndNotFixed()
    {
        $arguments = array(
            'count' => 5,
            'subject' => array(),
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function returnsEmptyResultForZeroCount()
    {
        $arguments = array(
            'count' => 0,
            'subject' => array('a', 'b', 'c', 'd', 'e'),
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function preservesArrayKeysIfRequested()
    {
        $arguments = array(
            'count' => 2,
            'subject' => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
            'preserveKeys' => true,
        );
        $result = $this->executeViewHelper($arguments);

        $expected = array(array('a' => 1, 'b' => 2), array('c' => 3, 'd' => 4), array('e' => 5));
        $this->assertEquals($expected, $result);
    }
}
