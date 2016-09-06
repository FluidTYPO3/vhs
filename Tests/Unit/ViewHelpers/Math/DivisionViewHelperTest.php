<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class DivisionViewHelperTest
 */
class DivisionViewHelperTest extends AbstractMathViewHelperTest
{

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
    public function testDualArgumentIteratorFirst()
    {
        $this->executeDualArgumentTest(array(4, 8), 2, array(2, 4));
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
    public function executeInvalidFirstArgumentTypeTest()
    {
        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Required argument "a" was not supplied');
        $this->executeViewHelper(array('b' => 1, 'fail' => true));
    }

    /**
     * @test
     */
    public function executeInvalidSecondArgumentTypeTest()
    {
        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Math operation attempted using an iterator $b against a numeric value $a. Either both $a and $b, or only $a, must be array/Iterator');
        $this->executeViewHelper(array('a' => 1, 'b' => array(1), 'fail' => true));
    }
}
