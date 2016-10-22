<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Extension;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class LoadedViewHelperTest
 */
class LoadedViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function rendersThenChildIfExtensionIsLoaded()
    {
        $arguments = [
            'extensionName' => 'Vhs',
            'then' => 1, '
			else' => 0
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertSame(1, $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildIfExtensionIsNotLoaded()
    {
        $arguments = [
            'extensionName' => 'Void',
             'then' => 1,
             'else' => 0
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertSame(0, $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
