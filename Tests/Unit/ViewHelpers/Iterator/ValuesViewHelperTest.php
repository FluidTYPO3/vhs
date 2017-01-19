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
 * Class ValuesViewHelperTest
 */
class ValuesViewHelperTest extends AbstractViewHelperTest
{
    /**
     * @test
     */
    public function returnsValuesUsingArgument()
    {
        $result = $this->executeViewHelper(['subject' => ['foo' => 'bar']]);
        $this->assertEquals(['bar'], $result);
    }

    /**
     * @test
     */
    public function supportsIterators()
    {
        $result = $this->executeViewHelper(['subject' => new \ArrayIterator(['foo' => 'bar'])]);
        $this->assertEquals(['bar'], $result);
    }
}
