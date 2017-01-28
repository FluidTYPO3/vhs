<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class HasSubpagesViewHelperTest
 */
class HasSubpagesViewHelperTest extends AbstractViewHelperTest
{

    public function testRenderWithAPageThatHasSubpages()
    {
        $pageService = $this->getMockBuilder(PageService::class)->setMethods(['getMenu'])->disableOriginalConstructor()->getMock();
        $pageService->expects($this->any())->method('getMenu')->will($this->returnValue(['childpage']));

        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'pageUid' => 1
        ];
        $instance = $this->buildViewHelperInstance($arguments);
        $instance::setPageService($pageService);
        $result = $instance->initializeArgumentsAndRender();
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderWithAPageWithoutSubpages()
    {
        $pageService = $this->getMockBuilder(PageService::class)->setMethods(['getMenu'])->disableOriginalConstructor()->getMock();
        $pageService->expects($this->any())->method('getMenu')->will($this->returnValue([]));

        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'pageUid' => 1
        ];
        $instance = $this->buildViewHelperInstance($arguments);
        $instance::setPageService($pageService);
        $result = $instance->initializeArgumentsAndRender();
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
