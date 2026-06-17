<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * Class RenderViewHelperTest
 */
class RenderViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender(): void
    {
        $this->renderingContext = $this->createRenderingContextWithRequest(new ServerRequest());
        $this->assertEmpty($this->executeViewHelper(['pageUid' => 1]));
    }
}
