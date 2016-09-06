<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TagViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ### Tag building ViewHelper
 *
 * Creates one HTML tag of any type, with various properties
 * like class and ID applied only if arguments are not empty,
 * rather than apply them always - empty or not - if provided.
 *
 * @package Vhs
 * @subpackage ViewHelpers
 */
class TagViewHelper extends AbstractTagBasedViewHelper
{

    use TagViewHelperTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('name', 'string', 'Tag name', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->arguments['class'] = trim((string) $this->arguments['class']);
        $this->arguments['class'] = str_replace(',', ' ', $this->arguments['class']);
        $content = $this->renderChildren();
        return $this->renderTag($this->arguments['name'], $content);
    }
}
