<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Configuration;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;

class RequestMiddlewaresTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function assetInclusionMiddlewareRunsInsideCoreCspMiddleware()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs']['disableAssetHandling'] = false;
        $middlewares = require dirname(__DIR__, 3) . '/Configuration/RequestMiddlewares.php';
        $after = $middlewares['frontend']['fluidtypo3/vhs/asset-inclusion']['after'];

        self::assertContains('typo3/cms-frontend/content-length-headers', $after);
        self::assertContains('typo3/cms-frontend/csp-headers', $after);
    }
}
