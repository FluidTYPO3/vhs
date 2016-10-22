<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Class SubtractViewHelperTest
 */
class SubtractViewHelperTest extends AbstractMathViewHelperTest
{

    /**
     * @test
     */
    public function testSingleArgumentIterator()
    {
        $this->executeSingleArgumentTest([8, 2], -10);
    }

    /**
     * @test
     */
    public function testDualArguments()
    {
        $this->executeDualArgumentTest(8, 2, 6);
    }

    /**
     * @test
     */
    public function executeMissingArgumentTest()
    {
        $this->setExpectedException(Exception::class, 'Required argument "b" was not supplied');
        $result = $this->executeViewHelper([]);
    }

    /**
     * @test
     */
    public function executeInvalidArgumentTypeTest()
    {
        $this->setExpectedException(Exception::class, 'Required argument "a" was not supplied');
        $result = $this->executeViewHelper(['b' => 1, 'fail' => true]);
    }
}
