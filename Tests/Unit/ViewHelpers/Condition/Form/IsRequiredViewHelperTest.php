<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Bar;
use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class IsRequiredViewHelperTest
 */
class IsRequiredViewHelperTest extends AbstractViewHelperTest
{

    public function testRenderThenWithSingleProperty()
    {
        $domainObject = new Foo();
        $arguments = [
            'property' => 'bar',
            'object' => $domainObject,
            'then' => 'then'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderElseWithSingleProperty()
    {
        $domainObject = new Foo();
        $arguments = [
            'property' => 'foo',
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderThenWithNestedSingleProperty()
    {
        $domainObject = new Bar();
        $arguments = [
            'property' => 'foo.bar',
            'object' => $domainObject,
            'then' => 'then'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderElseWithNestedSingleProperty()
    {
        $domainObject = new Bar();
        $arguments = [
            'property' => 'foo.foo',
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderThenWithNestedMultiProperty()
    {
        $domainObject = new Bar();
        $arguments = [
            'property' => 'bars.bar.foo.bar',
            'object' => $domainObject,
            'then' => 'then'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderElseWithNestedMultiProperty()
    {
        $domainObject = new Bar();
        $arguments = [
            'property' => 'bars.foo.foo',
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
