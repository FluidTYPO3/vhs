<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Gets values from an iterator, removing current keys (if any exist).
 */
class ValuesViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;
    use ArrayConsumingViewHelperTrait;
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('subject', 'mixed', 'The array/Traversable instance from which to get values');
        $this->registerAsArgument();
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $subject = static::arrayFromArrayOrTraversableOrCSVStatic(
            empty($arguments['as']) ? ($arguments['subject'] ?? $renderChildrenClosure()) : $arguments['subject']
        );
        $output = array_values($subject);
        /** @var string|null $as */
        $as = $arguments['as'];
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $output,
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
