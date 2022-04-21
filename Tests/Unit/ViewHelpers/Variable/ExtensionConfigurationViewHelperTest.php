<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class ExtensionConfigurationViewHelperTest
 */
class ExtensionConfigurationViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsNullIfVariableDoesNotExist()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs'] = ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
        $this->assertNull($this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'void']));
    }

    /**
     * @test
     */
    public function returnsNullIfVariableDoesNotExistLegacyExtConf()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vhs'] = ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
        $this->assertNull($this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'void']));
    }

    /**
     * @test
     */
    public function returnsDirectValueIfExists()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs'] = ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
        $this->assertEquals('test', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'test']));
    }

    /**
     * @test
     */
    public function returnsDirectValueIfExistsLegacyExtConf()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vhs'] = ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
        $this->assertEquals('test', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'test']));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExists()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs'] = ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
        $this->assertEquals('value', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'array.value']));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExistsLegacyExtConf()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vhs'] = ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
        $this->assertEquals('value', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'array.value']));
    }
}
