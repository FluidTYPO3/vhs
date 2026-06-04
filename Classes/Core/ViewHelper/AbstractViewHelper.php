<?php
declare(strict_types=1);

namespace FluidTYPO3\Vhs\Core\ViewHelper;

class AbstractViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @return mixed
     */
    public function render()
    {
        return static::renderStatic($this->arguments, $this->buildRenderChildrenClosure(), $this->renderingContext);
    }
}
