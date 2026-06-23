<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;

class ErrorUtilityTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function transfersPreviousException()
    {
        try {
            $previous = new \Exception('a', 23);
            ErrorUtility::throwViewHelperException('b', 42, $previous);
        } catch (\Exception $exception) {
            $this->assertEquals('b', $exception->getMessage());
            $this->assertEquals(42, $exception->getCode());

            $this->assertEquals('a', $exception->getPrevious()->getMessage());
            $this->assertEquals(23, $exception->getPrevious()->getCode());

            return;
        }
        $this->fail('No exception thrown');
    }
}
