<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class ValuesViewHelperTest
 */
class ValuesViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsValuesUsingArgument()
    {
        $result = $this->executeViewHelper(array('subject' => array('foo' => 'bar')));
        $this->assertEquals(array('bar'), $result);
    }

    /**
     * @test
     */
    public function returnsValuesUsingTagContent()
    {
        $result = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'test', array(), array('test' => array('foo' => 'bar')));
        $this->assertEquals(array('bar'), $result);
    }

    /**
     * @test
     */
    public function returnsValuesUsingTagContentAndAsArgument()
    {
        $result = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'test.0', array('as' => 'test', 'subject' => array('foo' => 'bar')), array());
        $this->assertEquals('bar', $result);
    }

    /**
     * @test
     */
    public function supportsIterators()
    {
        $result = $this->executeViewHelper(array('subject' => new \ArrayIterator(array('foo' => 'bar'))));
        $this->assertEquals(array('bar'), $result);
    }
}
