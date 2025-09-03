<?php
namespace FluidTYPO3\Vhs\Core\ViewHelper;

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollection;

class AbstractViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @return mixed
     */
    public function render()
    {
        return static::renderStatic(
            $this->arguments instanceof ArgumentCollection ? $this->arguments->getArrayCopy() : $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }
}
