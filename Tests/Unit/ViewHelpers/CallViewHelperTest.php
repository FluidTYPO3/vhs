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
        $this->executeViewHelper(['method' => 'method', 'arguments' => []]);
    }

    /**
     * @test
     */
    public function throwsRuntimeExceptionIfMethodNotFound()
    {
        $object = new \ArrayIterator(['foo', 'bar']);
        $this->setExpectedException('RuntimeException', null, 1356834755);
        $this->executeViewHelper(['method' => 'notfound', 'object' => $object, 'arguments' => []]);
    }

    /**
     * @test
     */
    public function executesMethodOnObjectFromArgument()
    {
        $object = new \ArrayIterator(['foo', 'bar']);
        $result = $this->executeViewHelper(['method' => 'count', 'object' => $object, 'arguments' => []]);
        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function executesMethodOnObjectFromChildContent()
    {
        $object = new \ArrayIterator(['foo', 'bar']);
        $result = $this->executeViewHelperUsingTagContent($object, ['method' => 'count', 'arguments' => []]);
        $this->assertEquals(2, $result);
    }
}
