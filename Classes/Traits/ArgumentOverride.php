<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

trait ArgumentOverride
{
    /**
     * TYPO3 13 / Fluid 4 compatibility: keep this signature untyped to remain
     * compatible with Fluid 4's AbstractViewHelper::overrideArgument().
     *
     * @param string $name
     * @param string $type
     * @param string $description
     * @param bool $required
     * @param mixed $defaultValue
     * @param bool|null $escape
     * @return static
     */
    protected function overrideArgument(
        $name,
        $type,
        $description,
        $required = false,
        $defaultValue = null,
        $escape = null
    ) {
        parent::registerArgument($name, $type, $description, $required, $defaultValue, $escape);
        return $this;
    }
}
