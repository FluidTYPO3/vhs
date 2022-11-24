<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

class DummyViewHelperNode extends ViewHelperNode
{
    public function __construct(ViewHelperInterface $viewHelper)
    {
        $this->uninitializedViewHelper = $viewHelper;
    }
}
