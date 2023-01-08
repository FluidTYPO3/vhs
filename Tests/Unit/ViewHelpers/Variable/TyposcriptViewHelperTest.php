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
use PHPUnit\Framework\Constraint\IsType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class TyposcriptViewHelperTest
 */
class TyposcriptViewHelperTest extends AbstractViewHelperTestCase
{
    private ?ConfigurationManagerInterface $configurationManager;

    protected function setUp(): void
    {
        $this->configurationManager = $this->getMockBuilder(ConfigurationManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configurationManager->method('getConfiguration')->willReturn(
            [
                'config' => [
                    'tx_extbase' => [
                        'features' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ]
        );

        GeneralUtility::setSingletonInstance(ConfigurationManager::class, $this->configurationManager);

        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsNullIfPathIsNull()
    {
        $this->assertNull($this->executeViewHelper(['path' => null]));
    }

    /**
     * @test
     */
    public function returnsArrayIfPathContainsArray()
    {
        $this->assertThat($this->executeViewHelper(['path' => 'config.tx_extbase.features']), new IsType(IsType::TYPE_ARRAY));
    }

    /**
     * @test
     */
    public function canGetPathUsingArgument()
    {
        $this->assertNotEmpty($this->executeViewHelper(['path' => 'config.tx_extbase.features']));
    }

    /**
     * @test
     */
    public function canGetPathUsingTagContent()
    {
        $this->assertNotEmpty($this->executeViewHelperUsingTagContent('config.tx_extbase.features'));
    }
}
