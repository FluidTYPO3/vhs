<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException;

/**
 * Class TemplateViewHelperTest
 */
class TemplateViewHelperTest extends AbstractViewHelperTest
{

    public function testRenderThrowsExceptionWithoutTemplatePath()
    {
        $this->setExpectedException(InvalidTemplateResourceException::class);
        $this->executeViewHelper(['variables' => []]);
    }
}
