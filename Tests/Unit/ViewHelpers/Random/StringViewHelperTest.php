<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class StringViewHelperTest
 */
class StringViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function generatesRandomStringWithDesiredCharactersOnlyAndOfDesiredLength()
    {
        $arguments = ['minimumLength' => 32, 'maximumLength' => 32, 'characters' => 'abcdef'];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(32, strlen($result));
        $this->assertEquals(0, preg_match('/[^a-f]+/', $result), 'Random string contained unexpected characters');
    }
}
