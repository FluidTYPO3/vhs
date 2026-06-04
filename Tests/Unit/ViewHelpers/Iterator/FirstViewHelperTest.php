<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class FirstViewHelperTest
 */
class FirstViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function returnsFirstElement(): void
    {
        $array = ['a', 'b', 'c'];
        $arguments = [
            'haystack' => $array
        ];
        $output = $this->executeViewHelper($arguments);
        $this->assertSame('a', $output);
    }

    /**
     * @test
     */
    public function supportsIterators(): void
    {
        $array = new \ArrayIterator(['a', 'b', 'c']);
        $arguments = [
            'haystack' => $array
        ];
        $output = $this->executeViewHelper($arguments);
        $this->assertSame('a', $output);
    }

    /**
     * @test
     */
    public function supportsTagContent(): void
    {
        $array = ['a', 'b', 'c'];
        $arguments = [
            'haystack' => null
        ];
        $output = $this->executeViewHelperUsingTagContent($array, $arguments);
        $this->assertSame('a', $output);
    }

    /**
     * @test
     */
    public function returnsNullIfHaystackIsNull(): void
    {
        $arguments = [
            'haystack' => null
        ];
        $output = $this->executeViewHelper($arguments);
        $this->assertNull($output);
    }

    /**
     * @test
     */
    public function returnsNullIfHaystackIsEmptyArray(): void
    {
        $arguments = [
            'haystack' => []
        ];
        $output = $this->executeViewHelper($arguments);
        $this->assertNull($output);
    }

    /**
     * @test
     */
    public function throwsExceptionOnUnsupportedHaystacks(): void
    {
        $arguments = [
            'haystack' => new \DateTime('now')
        ];
        $this->expectViewHelperException();
        $this->executeViewHelper($arguments);
    }
}
