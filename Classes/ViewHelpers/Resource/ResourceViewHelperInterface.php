<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Interface for Resource ViewHelpers
 */
interface ResourceViewHelperInterface
{

    /**
     * @param mixed $identity
     * @return mixed
     */
    public function getResource($identity);
}
