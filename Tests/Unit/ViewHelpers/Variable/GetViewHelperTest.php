<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class GetViewHelperTest
 */
class GetViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsNullIfVariableDoesNotExist()
    {
        $this->assertNull($this->executeViewHelper(array('name' => 'void', array())));
    }

    /**
     * @test
     */
    public function returnsDirectValueIfExists()
    {
        $this->assertEquals(1, $this->executeViewHelper(array('name' => 'test'), array('test' => 1)));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExists()
    {
        $this->assertEquals(1, $this->executeViewHelper(array('name' => 'test.test'), array('test' => array('test' => 1))));
    }

    /**
     * @test
     */
    public function returnsNestedValueUsingRawKeysIfRootExists()
    {
        $this->assertEquals(1, $this->executeViewHelper(array('name' => 'test.test', 'useRawKeys' => true), array('test' => array('test' => 1))));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExistsAndMembersAreNumeric()
    {
        $this->assertEquals(2, $this->executeViewHelper(array('name' => 'test.1'), array('test' => array(1, 2))));
    }

    /**
     * @test
     */
    public function returnsNullAndSuppressesExceptionOnInvalidPropertyGetting()
    {
        $user = new Foo();
        $this->assertEquals(null, $this->executeViewHelper(array('name' => 'test.void'), array('test' => $user)));
    }
}
