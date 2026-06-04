<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class NumberViewHelperTest
 */
class NumberViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function generatesRandomNumberWithoutDecimalsAsDefault(): void
    {
        $arguments = ['minimum' => 0, 'maximum' => 999999];
        $result = $this->executeViewHelper($arguments);
        self::assertIsInt($result);
        self::assertGreaterThanOrEqual(0, $result);
        self::assertLessThanOrEqual(999999, $result);
    }

    /**
     * @test
     */
    public function generatesRandomNumberWithoutDecimalsGivenArguments(): void
    {
        $arguments = [
            'minimum' => 0,
            'maximum' => 999999,
            'minimumDecimals' => 0,
            'maximumDecimals' => 0,
        ];
        $result = $this->executeViewHelper($arguments);
        self::assertIsInt($result);
        self::assertGreaterThanOrEqual(0, $result);
        self::assertLessThanOrEqual(999999, $result);
    }

    /**
     * @test
     */
    public function generatesRandomNumberWithDecimalsGivenArguments(): void
    {
        $arguments = [
            'minimum' => 0,
            'maximum' => 999999,
            'minimumDecimals' => 2,
            'maximumDecimals' => 8,
        ];
        $result = $this->executeViewHelper($arguments);
        self::assertIsFloat($result);
        self::assertGreaterThanOrEqual(0, $result);
        self::assertLessThan(1000000, $result);
    }
}
