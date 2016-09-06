<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class DefaultRenderMethodViewHelperTrait
 *
 * Trait implemented by ViewHelpers which are perfectly
 * fine with a render() method which only delegates to
 * renderStatic().
 *
 * Do not implement in ViewHelpers which subclass from
 * other ViewHelpers if any parent implements a render()
 * method.
 *
 * Has the following main responsibilities:
 *
 * - generic render method passing arguments, context and closures
 *   to renderStatic.
 */
trait DefaultRenderMethodViewHelperTrait
{

    /**
     * Delegation to renderStatic
     *
     * @return mixed
     */
    public function render()
    {
        return static::renderStatic($this->arguments, $this->buildRenderChildrenClosure(), $this->renderingContext);
    }
}
