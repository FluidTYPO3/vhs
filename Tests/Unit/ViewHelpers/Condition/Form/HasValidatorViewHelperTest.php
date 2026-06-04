<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\FileRepositoryProxy;
use FluidTYPO3\Vhs\Proxy\ResourceFactoryProxy;
use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Bar;
use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

class HasValidatorViewHelperTest extends AbstractViewHelperTestCase
{

    protected function setUp(): void
    {
        $this->singletonInstances[ResourceFactoryProxy::class] = $this->getMockBuilder(ResourceFactoryProxy::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[FileRepositoryProxy::class] = $this->getMockBuilder(FileRepositoryProxy::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[ReflectionService::class] = new ReflectionService(new NullFrontend('testing'), 'testing');

        parent::setUp();
    }

    protected function getInstanceOfFoo(): Foo
    {
        return new Foo();
    }

    protected function getNestedPathToFoo(): string
    {
        return 'foo';
    }

    public function testRenderElseWithSingleProperty(): void
    {
        $domainObject = $this->getInstanceOfFoo();
        $arguments = [
            'validatorName' => 'NotEmpty',
            'property' => $this->getNestedPathToFoo(),
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }

    public function testRenderElseWithNestedSingleProperty(): void
    {
        $domainObject = new Bar();
        $prefix = $this->getNestedPathToFoo();
        $arguments = [
            'validatorName' => 'NotEmpty',
            'property' => $prefix . '.foo',
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }

    public function testRenderElseWithNestedMultiProperty(): void
    {
        $domainObject = new Bar();
        $prefix = $this->getNestedPathToFoo();
        $arguments = [
            'validatorName' => 'NotEmpty',
            'property' => 'bars.' . $prefix . '.foo',
            'object' => $domainObject,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }
}
