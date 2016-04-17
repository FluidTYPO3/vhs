<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class BasicViewHelperTrait
 *
 * Trait implemented by ViewHelpers which require access
 * to generic functions.
 *
 * Has the following main responsibilities:
 *
 * - generic method to get either an argument or if that
 *   argument is not specified, retrieve the tag contents.
 */
trait BasicViewHelperTrait
{

    /**
     * Retrieve an argument either from arguments if
     * specified there, else from tag content.
     *
     * @param string $argumentName
     * @return mixed
     */
    protected function getArgumentFromArgumentsOrTagContent($argumentName)
    {
        if (false === $this->hasArgument($argumentName)) {
            $value = $this->renderChildren();
        } else {
            $value = $this->arguments[$argumentName];
        }
        return $value;
    }
}
