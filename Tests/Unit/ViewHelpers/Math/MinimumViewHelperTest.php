<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class MinimumViewHelperTest
 */
class MinimumViewHelperTest extends AbstractMathViewHelperTest
{

    /**
     * @test
     */
    public function testSingleArgument()
    {
        $this->executeSingleArgumentTest(array(1, 3), 1);
    }

    /**
     * @test
     */
    public function testDualArgument()
    {
        $this->executeDualArgumentTest(4, 2, 2);
    }

    /**
     * @test
     */
    public function testDualArgumentBothIterators()
    {
        $this->executeDualArgumentTest(array(4, 8), array(8, 8), array(4, 8));
    }

    /**
     * @test
     */
    public function executeMissingArgumentTest()
    {
        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Required argument "b" was not supplied');
        $this->executeViewHelper(array());
    }

    /**
     * @test
     */
    public function executeInvalidArgumentTypeTest()
    {
        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Required argument "a" was not supplied');
        $this->executeViewHelper(array('b' => 1, 'fail' => true));
    }
}
