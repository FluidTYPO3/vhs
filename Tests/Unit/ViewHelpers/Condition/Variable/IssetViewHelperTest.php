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
 * Class IssetViewHelperTest
 */
class IssetViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function rendersThenChildIfVariableIsSet()
    {
        $arguments = array(
            'name' => 'test',
            'then' => 'then',
            'else' => 'else'
        );
        $variables = array(
            'test' => true
        );
        $result = $this->executeViewHelper($arguments, $variables);
        $this->assertEquals($arguments['then'], $result);

        $staticResult = $this->executeViewHelperStatic($arguments, $variables);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildIfVariableIsNotSet()
    {
        $arguments = array(
            'name' => 'test',
            'then' => 'then',
            'else' => 'else'
        );
        $variables = array();
        $result = $this->executeViewHelper($arguments, $variables);
        $this->assertEquals($arguments['else'], $result);

        $staticResult = $this->executeViewHelperStatic($arguments, $variables);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
