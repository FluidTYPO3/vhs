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
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;

/**
 * Class SetViewHelperTest
 */
class SetViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function canSetVariable(): void
    {
        $variables = ['test' => true];
        $result = $this->executeViewHelper(['name' => 'test', 'value' => false], $variables);
        $this->assertNull($result);
        $this->assertFalse($this->getTemplateVariableContainer()->get('test'));
    }

    /**
     * @test
     */
    public function canSetVariableInExistingArrayValue(): void
    {
        $variables = ['test' => ['test' => true]];
        $result = $this->executeViewHelper(['name' => 'test.test', 'value' => false], $variables);
        $this->assertNull($result);
        $this->assertFalse($this->getTemplateVariableContainer()->get('test.test'));
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootDoesNotExist(): void
    {
        $result = $this->executeViewHelper(['name' => 'doesnotexist.test', 'value' => false]);
        $this->assertNull($this->getTemplateVariableContainer()->get('test.test'));
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootDoesNotAllowSetting(): void
    {
        $domainObject = new Foo();
        $variables = ['test' => $domainObject];
        $result = $this->executeViewHelper(['name' => 'test.propertydoesnotexist', 'value' => false], $variables);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootPropertyNameIsInvalid(): void
    {
        $variables = ['test' => 'test'];
        $result = $this->executeViewHelper(['name' => 'test.test', 'value' => false], $variables);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function canSetVariableWithValueFromTagContent(): void
    {
        $variables = ['test' => true];
        $result = $this->executeViewHelperUsingTagContent(false, ['name' => 'test'], $variables);
        $this->assertNull($result);
        $this->assertFalse($this->getTemplateVariableContainer()->get('test'));
    }

    private function getTemplateVariableContainer(): StandardVariableProvider
    {
        self::assertInstanceOf(StandardVariableProvider::class, $this->templateVariableContainer);
        return $this->templateVariableContainer;
    }
}
