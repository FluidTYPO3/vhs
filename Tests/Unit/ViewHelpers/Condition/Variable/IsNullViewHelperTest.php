<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class IsNullViewHelperTest
 */
class IsNullViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function rendersThenChildIfVariableIsNull()
    {
        $arguments = [
            'value' => null,
            'then' => 'then',
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals($arguments['then'], $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildIfVariableIsNotNull()
    {
        $arguments = [
            'value' => true,
            'then' => 'then',
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals($arguments['else'], $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
