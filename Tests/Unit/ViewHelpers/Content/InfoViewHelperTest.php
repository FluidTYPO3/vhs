<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class InfoViewHelperTest
 */
class InfoViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender()
    {
        $record = ['uid' => 1];

        $contentObject = $this->getMockBuilder(ContentObjectRenderer::class)
            ->onlyMethods(['getCurrentTable'])
            ->disableOriginalConstructor()
            ->getMock();
        $contentObject->data = $record;
        $contentObject->method('getCurrentTable')->willReturn('tt_content');

        $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
            ->onlyMethods(['getContentObject'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $configurationManager->method('getContentObject')->willReturn($contentObject);

        $instance = $this->createInstance();
        $arguments = $this->buildViewHelperArguments($instance, []);
        $instance->setArguments($arguments);
        $instance->injectConfigurationManager($configurationManager);

        $output = $instance->render();
        self::assertSame($record, $output);
    }
}
