<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class ExtensionConfigurationViewHelperTest
 */
class ExtensionConfigurationViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function returnsNullIfVariableDoesNotExist(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs'] = $this->createExtensionConfiguration();
        $this->assertNull($this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'void']));
    }

    /**
     * @test
     */
    public function returnsNullIfVariableDoesNotExistLegacyExtConf(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vhs'] = $this->createExtensionConfiguration();
        $this->assertNull($this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'void']));
    }

    /**
     * @test
     */
    public function returnsDirectValueIfExists(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs'] = $this->createExtensionConfiguration();
        $this->assertEquals('test', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'test']));
    }

    /**
     * @test
     */
    public function returnsDirectValueIfExistsLegacyExtConf(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vhs'] = $this->createExtensionConfiguration();
        $this->assertEquals('test', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'test']));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExists(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs'] = $this->createExtensionConfiguration();
        $this->assertEquals('value', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'array.value']));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExistsLegacyExtConf(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vhs'] = $this->createExtensionConfiguration();
        $this->assertEquals('value', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'array.value']));
    }

    private function createExtensionConfiguration(): array
    {
        return ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
    }
}
