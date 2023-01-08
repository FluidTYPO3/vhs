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
use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\LegacyFoo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * Class IsRequiredViewHelperTest
 */
class IsRequiredViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $this->singletonInstances[ReflectionService::class] = $this->getMockBuilder(ReflectionService::class)
            ->setMethods(['__destruct'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();
    }

    protected function getInstanceOfFoo()
    {
        if (version_compare(TYPO3_version, '9.3', '>=')) {
            return new Foo();
        }
        return new LegacyFoo();
    }

    protected function getNestedPathToFoo()
    {
        if (version_compare(TYPO3_version, '9.3', '>=')) {
            return 'foo';
        }
        return 'legacyFoo';
    }

    public function testRenderElseWithSingleProperty()
    {
        $domainObject = $this->getInstanceOfFoo();
        $arguments = [
            'property' => $this->getNestedPathToFoo(),
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }

    public function testRenderElseWithNestedSingleProperty()
    {
        $domainObject = new Bar();
        $prefix = $this->getNestedPathToFoo();
        $arguments = [
            'property' => $prefix . '.foo',
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }

    public function testRenderElseWithNestedMultiProperty()
    {
        $domainObject = new Bar();
        $prefix = $this->getNestedPathToFoo();
        $arguments = [
            'property' => 'bars.' . $prefix . '.foo',
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }
}
