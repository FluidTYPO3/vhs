<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class AverageViewHelperTest
 */
class AverageViewHelperTest extends AbstractMathViewHelperTest
{

    /**
     * @test
     */
    public function testSingleArgument()
    {
        $this->executeSingleArgumentTest(1, 1);
    }

    /**
     * @test
     */
    public function testSingleArgumentIteratorFirst()
    {
        $this->executeSingleArgumentTest(array(1, 3), 2);
    }

    /**
     * @test
     */
    public function testDualArgument()
    {
        $this->executeDualArgumentTest(1, 3, 2);
    }

    /**
     * @test
     */
    public function testDualArgumentWithIteratorFirst()
    {
        $this->executeDualArgumentTest(array(1, 5), 3, array(2, 4));
    }

    /**
     * @test
     */
    public function testDualArgumentBothIterators()
    {
        $this->executeDualArgumentTest(array(1, 5), array(3, 3), array(2, 4));
    }

    /**
     * @test
     */
    public function executeMissingArgumentTest()
    {
        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Required argument "b" was not supplied');
        $this->executeViewHelper(array());
    }
}
