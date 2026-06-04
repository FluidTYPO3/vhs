<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Content\AbstractContentViewHelper;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Page\PageInformation;

class AbstractContentViewHelperTest extends AbstractViewHelperTestCase
{
    public function testGetCurrentPageRecordUsesRenderingContextRequest(): void
    {
        $globalPageInformation = new PageInformation();
        $globalPageInformation->setPageRecord(['uid' => 111, 'title' => 'Outer page']);
        $subRequestPageInformation = new PageInformation();
        $subRequestPageInformation->setPageRecord(['uid' => 222, 'title' => 'Inner page']);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.page.information',
            $globalPageInformation
        );

        $instance = $this->createInstance();
        self::assertInstanceOf(AbstractContentViewHelper::class, $instance);
        $instance->setRenderingContext(
            $this->createRenderingContextWithRequest(
                (new ServerRequest())->withAttribute('frontend.page.information', $subRequestPageInformation)
            )
        );

        self::assertSame(
            ['uid' => 222, 'title' => 'Inner page'],
            $this->callInaccessibleMethod($instance, 'getCurrentPageRecord')
        );
    }
}
