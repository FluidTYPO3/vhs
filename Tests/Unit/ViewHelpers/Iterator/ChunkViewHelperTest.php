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
        $arguments = [
            'count' => 5,
            'fixed' => true,
            'subject' => ['a', 'b', 'c', 'd', 'e'],
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(5, $result);
    }

    /**
     * @test
     */
    public function returnsConfiguredItemNumberIfFixedAndSubjectIsEmpty()
    {
        $arguments = [
            'count' => 5,
            'fixed' => true,
            'subject' => [],
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(5, $result);
    }

    /**
     * @test
     */
    public function returnsExpectedItemNumberIfNotFixed()
    {
        $arguments = [
            'count' => 4,
            'subject' => ['a', 'b', 'c', 'd', 'e'],
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(2, $result);
    }

    /**
     * @test
     */
    public function returnsEmptyResultForEmptySubjectAndNotFixed()
    {
        $arguments = [
            'count' => 5,
            'subject' => [],
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function returnsEmptyResultForZeroCount()
    {
        $arguments = [
            'count' => 0,
            'subject' => ['a', 'b', 'c', 'd', 'e'],
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function preservesArrayKeysIfRequested()
    {
        $arguments = [
            'count' => 2,
            'subject' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'preserveKeys' => true,
        ];
        $result = $this->executeViewHelper($arguments);

        $expected = [['a' => 1, 'b' => 2], ['c' => 3, 'd' => 4], ['e' => 5]];
        $this->assertEquals($expected, $result);
    }
}
