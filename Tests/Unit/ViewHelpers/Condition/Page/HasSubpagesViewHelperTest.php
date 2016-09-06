<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class HasSubpagesViewHelperTest
 */
class HasSubpagesViewHelperTest extends AbstractViewHelperTest
{

    public function testRenderWithAPageThatHasSubpages()
    {
        $pageService = $this->getMock('FluidTYPO3\Vhs\Service\PageService', array('getMenu'), array(), '', false);
        $pageService->expects($this->any())->method('getMenu')->will($this->returnValue(array('childpage')));

        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'pageUid' => 1
        );
        $instance = $this->buildViewHelperInstance($arguments);
        $instance::setPageService($pageService);
        $result = $instance->initializeArgumentsAndRender();
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    public function testRenderWithAPageWithoutSubpages()
    {
        $pageService = $this->getMock('FluidTYPO3\Vhs\Service\PageService', array('getMenu'), array(), '', false);
        $pageService->expects($this->any())->method('getMenu')->will($this->returnValue(array()));

        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'pageUid' => 1
        );
        $instance = $this->buildViewHelperInstance($arguments);
        $instance::setPageService($pageService);
        $result = $instance->initializeArgumentsAndRender();
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
