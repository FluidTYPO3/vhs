<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class HeaderViewHelperTest
 */
class HeaderViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender()
    {
        $singletons = GeneralUtility::getSingletonInstances();
        GeneralUtility::setSingletonInstance(
            PageRenderer::class,
            $this->getMockBuilder(PageRenderer::class)->disableOriginalConstructor()->getMock()
        );
        $this->assertEmpty($this->executeViewHelper());
        GeneralUtility::resetSingletonInstances($singletons);
    }
}
