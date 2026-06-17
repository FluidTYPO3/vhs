<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\FileRepositoryProxy;
use FluidTYPO3\Vhs\Proxy\ResourceFactoryProxy;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class FalViewHelperTest
 */
class FalViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $this->singletonInstances[ResourceFactoryProxy::class] = $this->getMockBuilder(ResourceFactoryProxy::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[FileRepositoryProxy::class] = $this->getMockBuilder(FileRepositoryProxy::class)
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();
    }

    public function testGetActiveRecordReturnsRequestPageRecord(): void
    {
        $pageInformation = new PageInformation();
        $pageInformation->setId(1);
        $pageInformation->setPageRecord(['uid' => 1, 'title' => 'Page']);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('frontend.page.information', $pageInformation);

        $subject = new FalViewHelper();
        $subject->setRenderingContext($this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']));

        self::assertSame(['uid' => 1, 'title' => 'Page'], $subject->getActiveRecord());
    }

    public function testGetActiveRecordUsesRenderingContextRequest(): void
    {
        $globalPageInformation = new PageInformation();
        $globalPageInformation->setPageRecord(['uid' => 111, 'title' => 'Outer page']);
        $subRequestPageInformation = new PageInformation();
        $subRequestPageInformation->setPageRecord(['uid' => 222, 'title' => 'Inner page']);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.page.information',
            $globalPageInformation
        );

        $subject = new FalViewHelper();
        $subject->setRenderingContext(
            $this->createRenderingContextWithRequest(
                (new ServerRequest())->withAttribute('frontend.page.information', $subRequestPageInformation)
            )
        );

        self::assertSame(['uid' => 222, 'title' => 'Inner page'], $subject->getActiveRecord());
    }
}
