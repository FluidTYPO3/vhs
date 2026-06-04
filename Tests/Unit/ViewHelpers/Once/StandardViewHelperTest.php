<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Cache\CacheInstruction;

/**
 * Class StandardViewHelperTest
 */
class StandardViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function disablesCacheOnRenderingContextRequest(): void
    {
        $globalCacheInstruction = new CacheInstruction();
        $subRequestCacheInstruction = new CacheInstruction();
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.cache.instruction', $globalCacheInstruction);
        $subRequest = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.cache.instruction', $subRequestCacheInstruction);

        $viewHelper = $this->createInstance();
        $viewHelper->setRenderingContext($this->createRenderingContextWithRequest($subRequest));

        $this->callInaccessibleMethod($viewHelper, 'renderThenChild');

        self::assertSame([], $globalCacheInstruction->getDisabledCacheReasons());
        self::assertNotSame([], $subRequestCacheInstruction->getDisabledCacheReasons());
    }
}
