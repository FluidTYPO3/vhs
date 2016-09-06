<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class CallViewHelperTest
 */
class CallViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function throwsRuntimeExceptionIfObjectNotFound()
    {
        $this->setExpectedException('RuntimeException', null, 1356849652);
        $this->executeViewHelper(array('method' => 'method', 'arguments' => array()));
    }

    /**
     * @test
     */
    public function throwsRuntimeExceptionIfMethodNotFound()
    {
        $object = new \ArrayIterator(array('foo', 'bar'));
        $this->setExpectedException('RuntimeException', null, 1356834755);
        $this->executeViewHelper(array('method' => 'notfound', 'object' => $object, 'arguments' => array()));
    }

    /**
     * @test
     */
    public function executesMethodOnObjectFromArgument()
    {
        $object = new \ArrayIterator(array('foo', 'bar'));
        $result = $this->executeViewHelper(array('method' => 'count', 'object' => $object, 'arguments' => array()));
        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function executesMethodOnObjectFromChildContent()
    {
        $object = new \ArrayIterator(array('foo', 'bar'));
        $result = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', array('method' => 'count', 'arguments' => array()), array('v' => $object));
        $this->assertEquals(2, $result);
    }
}
