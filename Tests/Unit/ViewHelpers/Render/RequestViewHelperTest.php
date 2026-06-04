<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Render\RequestViewHelper;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class RequestViewHelperTest
 */
class RequestViewHelperTest extends AbstractViewHelperTestCase
{
    public function testLoadDefaultValuesUsesRenderingContextRequest(): void
    {
        $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMock();
        $configurationManager->method('getConfiguration')->willReturn(
            [
                'controllerConfiguration' => [
                    'Vendor\\Extension\\Controller\\ExampleController' => [
                        'alias' => 'Example',
                        'className' => 'Vendor\\Extension\\Controller\\ExampleController',
                        'actions' => ['list'],
                    ],
                ],
            ]
        );
        GeneralUtility::setSingletonInstance(ConfigurationManagerInterface::class, $configurationManager);

        $GLOBALS['TYPO3_REQUEST'] = new ServerRequest('https://outer.example/request-111');
        $renderingContext = $this->createRenderingContextWithRequest(
            new ServerRequest('https://inner.example/request-222')
        );

        $method = new \ReflectionMethod(RequestViewHelper::class, 'loadDefaultValues');
        $method->setAccessible(true);
        $request = $method->invokeArgs(
            null,
            [$renderingContext, 'Extension', 'Plugin', 'Example', 'list', []]
        );
        self::assertInstanceOf(ServerRequest::class, $request);

        self::assertSame('inner.example', $request->getUri()->getHost());
    }
}
