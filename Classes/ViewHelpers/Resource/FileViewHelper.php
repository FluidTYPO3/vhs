<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;

/**
 * ViewHelper to output or assign FAL sys_file records.
 */
class FileViewHelper extends AbstractResourceViewHelper
{

    use TemplateVariableViewHelperTrait;

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerAsArgument();
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $files = $this->getFiles(true);
        if (1 === count($files)) {
            $files = array_shift($files);
        }
        return $this->renderChildrenWithVariableOrReturnInput($files);
    }
}
