<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class WordWrapViewHelperTest
 */
class WordWrapViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function willWrapStringAccordingToArguments()
    {
        $content = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis, et id ipsum modi molestiae molestias numquam! Aperiam assumenda commodi ducimus harum iure nostrum odit, vel voluptatem! Beatae commodi qui rem!';
        $arguments = [
            'limit' => 25,
            'break' => PHP_EOL,
            'glue' => '|',
        ];
        $test = $this->executeViewHelperUsingTagContent($content, $arguments);
        $this->assertRegExp('/.{0,25}\|/', $test);
    }
}
