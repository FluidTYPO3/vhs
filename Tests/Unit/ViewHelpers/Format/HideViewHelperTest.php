<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class HideViewHelperTest
 */
class HideViewHelperTest extends AbstractViewHelperTestCase
{
    #[Test]
    public function hidesTagContent(): void
    {
        $test = $this->executeViewHelperUsingTagContent('this is hidden');
        $this->assertNull($test);
    }

    #[Test]
    public function canBeDisabled(): void
    {
        $test = $this->executeViewHelperUsingTagContent('this is shown', ['disabled' => true]);
        $this->assertSame('this is shown', $test);
    }
}
