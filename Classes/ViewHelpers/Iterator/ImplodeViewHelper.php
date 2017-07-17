<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Implode ViewHelper
 *
 * Implodes an array or array-convertible object by $glue.
 */
class ImplodeViewHelper extends ExplodeViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var string
     */
    protected static $method = 'implode';

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument('content', 'array', 'Array or array-convertible object to be imploded by glue');
    }
}
