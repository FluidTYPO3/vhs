<?php

namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

        $extConfUnderTest = ['foo' => 'bar', 'test' => 'test', 'array' => ['value' => 'value']];
        if (version_compare(TYPO3_version, '8.0', '<')) {
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vhs'] = $extConfUnderTest;
        } else {
            /** @var ExtensionConfiguration $extConf */
            $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class);
            $extConf->set('vhs', '', $extConfUnderTest);
        }
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
