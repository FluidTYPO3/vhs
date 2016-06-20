<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Form\Select;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Optgroup ViewHelper to use under vhs:form.select.
 */
class OptgroupViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'optgroup';

    /**
     * Initialize
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('label', 'string', 'Label for this option group');
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->tag->setContent($this->renderChildren());
        return $this->tag->render();
    }
}
