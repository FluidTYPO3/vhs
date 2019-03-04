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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class HasValidatorViewHelperTest
 */
class HasValidatorViewHelperTest extends AbstractViewHelperTest
{

    protected function getInstanceOfFoo()
    {
        if (version_compare(ExtensionManagementUtility::getExtensionVersion('fluid'), 9.3, '>=')) {
            return new Foo();
        }
        return new LegacyFoo();
    }

    protected function getNestedPathToFoo()
    {
        if (version_compare(ExtensionManagementUtility::getExtensionVersion('fluid'), 9.3, '>=')) {
            return 'foo';
        }
        return 'legacyFoo';
    }

    public function testRenderElseWithSingleProperty()
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

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderElseWithNestedSingleProperty()
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

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderElseWithNestedMultiProperty()
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

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
