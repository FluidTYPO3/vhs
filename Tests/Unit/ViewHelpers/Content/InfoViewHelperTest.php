<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Content\InfoViewHelper;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class InfoViewHelperTest
 */
class InfoViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender(): void
    {
        $record = ['uid' => 1];

        $contentObject = $this->getMockBuilder(ContentObjectRenderer::class)
            ->onlyMethods(['getCurrentTable'])
            ->disableOriginalConstructor()
            ->getMock();
        $contentObject->data = $record;
        $contentObject->method('getCurrentTable')->willReturn('tt_content');

        if (method_exists(ConfigurationManagerInterface::class, 'getContentObject')) {
            /** @var ConfigurationManagerInterface&MockObject $configurationManager */
            $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMock();
            $configurationManager->method('getContentObject')->willReturn($contentObject);
        } else {
            /** @var ServerRequestInterface&MockObject $request */
            $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
            $request->method('getAttribute')->willReturn($contentObject);
            /** @var ConfigurationManagerInterface&MockObject $configurationManager */
            $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
                ->onlyMethods(['getConfiguration', 'setConfiguration', 'setRequest'])
                ->addMethods(['getRequest'])
                ->getMock();
            $configurationManager->method('getRequest')->willReturn($request);
        }

        $instance = $this->createInstance();
        self::assertInstanceOf(InfoViewHelper::class, $instance);
        $arguments = $this->buildViewHelperArguments($instance, []);
        $instance->setArguments($arguments);
        $instance->injectConfigurationManager($configurationManager);

        $output = $instance->render();
        self::assertSame($record, $output);
    }

    public function testResolveCurrentRecordReferenceReturnsNullWithoutRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        $instance = $this->createInstance();
        self::assertInstanceOf(InfoViewHelper::class, $instance);
        $instance->setRenderingContext($this->createRenderingContextWithoutRequest());

        self::assertNull($this->callInaccessibleMethod($instance, 'resolveCurrentRecordReference'));
    }

    public function testResolveCurrentRecordReferenceUsesRenderingContextRequest(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.controller',
            (object) ['currentRecord' => 'tt_content:111']
        );
        $subRequest = (new ServerRequest())->withAttribute(
            'frontend.controller',
            (object) ['currentRecord' => 'tt_content:222']
        );

        $instance = $this->createInstance();
        self::assertInstanceOf(InfoViewHelper::class, $instance);
        $instance->setRenderingContext($this->createRenderingContextWithRequest($subRequest));

        self::assertSame(
            'tt_content:222',
            $this->callInaccessibleMethod($instance, 'resolveCurrentRecordReference')
        );
    }
}
