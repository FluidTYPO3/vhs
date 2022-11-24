<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class IssetViewHelperTest
 */
class IssetViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        if (version_compare(TYPO3_version, '8.0', '<')) {
            $this->markTestSkipped('Skipped, ViewHelper does not work on 7.6');
        }
        parent::setUp();
    }

    /**
     * @test
     */
    public function rendersThenChildIfVariableIsSet()
    {
        $arguments = [
            'name' => 'test',
            'then' => 'then',
            'else' => 'else'
        ];
        $variables = [
            'test' => true
        ];
        $result = $this->executeViewHelper($arguments, $variables);
        $this->assertEquals($arguments['then'], $result);
    }

    /**
     * @test
     */
    public function rendersElseChildIfVariableIsNotSet()
    {
        $arguments = [
            'name' => 'test',
            'then' => 'then',
            'else' => 'else'
        ];
        $variables = [];
        $result = $this->executeViewHelper($arguments, $variables);
        $this->assertEquals($arguments['else'], $result);
    }
}
