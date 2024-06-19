<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper;
use FluidTYPO3\Vhs\ViewHelpers\MenuViewHelper;

class SubViewHelperTest extends AbstractViewHelperTestCase
{
    public function testReturnsNullWithoutParentInstance(): void
    {
        self::assertNull($this->executeViewHelper(['pageUid' => 1]));
    }

    public function testReturnsNullIfNotExpandedActiveOrCurrent(): void
    {
        $arguments = ['pageUid' => 1, 'expandAll' => false];

        $parent = $this->getMockBuilder(MenuViewHelper::class)
            ->setMethods(['getMenuArguments'])
            ->disableOriginalConstructor()
            ->getMock();
        $parent->method('getMenuArguments')->willReturn($arguments);

        $this->viewHelperVariableContainer->add(AbstractMenuViewHelper::class, 'parentInstance', $parent);

        $pageService = $this->getMockBuilder(PageService::class)
            ->setMethods(['isActive', 'isCurrent'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('isActive')->willReturn(false);
        $pageService->method('isCurrent')->willReturn(false);

        $subject = $this->buildViewHelperInstance($arguments);

        $subject->injectPageService($pageService);

        $output = $this->executeInstance($subject, $arguments);

        self::assertNull($output);
    }

    public function testRendersMenu(): void
    {
        $arguments = ['pageUid' => 1, 'expandAll' => false];

        $parent = $this->getMockBuilder(MenuViewHelper::class)
            ->setMethods(['getMenuArguments', 'render'])
            ->disableOriginalConstructor()
            ->getMock();
        $parent->method('getMenuArguments')->willReturn($arguments);
        $parent->method('render')->willReturn('rendered');

        $this->viewHelperVariableContainer->add(AbstractMenuViewHelper::class, 'parentInstance', $parent);
        $this->viewHelperVariableContainer->add(AbstractMenuViewHelper::class, 'variables', ['menu' => []]);

        $pageService = $this->getMockBuilder(PageService::class)
            ->setMethods(['isActive', 'isCurrent'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('isActive')->willReturn(false);
        $pageService->method('isCurrent')->willReturn(true);

        $subject = $this->buildViewHelperInstance($arguments, ['menu' => [['uid' => 1]]]);

        $subject->injectPageService($pageService);

        $output = $this->executeInstance($subject, $arguments);

        self::assertSame('rendered', $output);
    }
}
