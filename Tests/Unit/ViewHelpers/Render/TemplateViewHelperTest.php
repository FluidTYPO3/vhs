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
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Class TemplateViewHelperTest
 */
class TemplateViewHelperTest extends AbstractViewHelperTestCase
{
    private ?ViewInterface $view;

    protected function setUp(): void
    {
        $this->view = $this->getMockBuilder(StandaloneView::class)->disableOriginalConstructor()->getMock();
        $this->view->method('render')->willThrowException(new InvalidTemplateResourceException('test'));

        parent::setUp();
    }

    public function testRenderThrowsExceptionWithoutTemplatePath()
    {
        $this->expectException(InvalidTemplateResourceException::class);
        $this->executeViewHelper(['variables' => []]);
    }

    protected function createObjectManagerInstance(): ObjectManagerInterface
    {
        $instance = parent::createObjectManagerInstance();
        $instance->method('get')->willReturnMap(
            [
                [StandaloneView::class, $this->view],
            ]
        );
        return $instance;
    }
}
