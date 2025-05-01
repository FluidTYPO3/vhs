<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Security\DenyViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class DenyViewHelperTest extends AbstractViewHelperTestCase
{
    public function testInvertsDecision(): void
    {
        $GLOBALS['BE_USER'] = (object) [
            'user' => ['uid' => 1],
        ];
        $viewHelper = $this->getMockBuilder(DenyViewHelper::class)
            ->addMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments = $this->buildViewHelperArguments($viewHelper, ['anyBackendUser' => true]);
        $this->createViewHelperNode($viewHelper, $arguments, [$this->createNode('Text', 'protected')]);
        $viewHelper->setArguments($arguments);

        $viewHelper->setRenderingContext(
            $this->getMockBuilder(RenderingContextInterface::class)->getMock()
        );

        GeneralUtility::addInstance(get_class($viewHelper), $viewHelper);
        self::assertEmpty($viewHelper->render());
    }
}
