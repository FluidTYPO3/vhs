<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

/*
 * This file is part of the FluidTYPO3/Flux project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;
use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentProcessorInterface;

class PassthroughArgumentProcessor implements ArgumentProcessorInterface
{
    #[\Override] public function process(mixed $value, ArgumentDefinition $definition): mixed
    {
        return $value;
    }

    #[\Override] public function isValid(mixed $value, ArgumentDefinition $definition): bool
    {
        return true;
    }
}
