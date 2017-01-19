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
 * Class ExtensionConfigurationViewHelperTest
 */
class ExtensionConfigurationViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vhs'] = ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
    }

    /**
     * @test
     */
    public function returnsNullIfVariableDoesNotExist()
    {
        $this->assertNull($this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'void']));
    }

    /**
     * @test
     */
    public function returnsDirectValueIfExists()
    {
        $this->assertEquals('test', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'test']));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExists()
    {
        $this->assertEquals('value', $this->executeViewHelper(['extensionKey' => 'vhs', 'path' => 'array.value']));
    }
}
