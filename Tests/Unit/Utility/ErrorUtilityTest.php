<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use Throwable;

class ErrorUtilityTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function transfersPreviousException(): void
    {
        $exception = null;
        $previous = new \Exception('a', 23);
        $throwException = static function () use ($previous): void {
            ErrorUtility::throwViewHelperException('b', 42, $previous);
        };

        try {
            $throwException();
        } catch (Throwable $throwable) {
            $exception = $throwable;
        }

        self::assertInstanceOf(Throwable::class, $exception);
        self::assertSame('b', $exception->getMessage());
        self::assertSame(42, $exception->getCode());

        $previousException = $exception->getPrevious();
        self::assertInstanceOf(Throwable::class, $previousException);
        self::assertSame('a', $previousException->getMessage());
        self::assertSame(23, $previousException->getCode());
    }
}
