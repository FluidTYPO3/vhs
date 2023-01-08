<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\PageRendererTrait;
use FluidTYPO3\Vhs\Traits\TagViewHelperTrait;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper used to render a link tag in the `<head>` section of the page.
 * If you use the ViewHelper in a plugin, the plugin and its action have to
 * be cached!
 */
class LinkViewHelper extends AbstractTagBasedViewHelper
{
    use TagViewHelperTrait;
    use PageRendererTrait;

    /**
     * @var    string
     */
    protected $tagName = 'link';

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerTagAttribute('rel', 'string', 'Property: rel');
        $this->registerTagAttribute('href', 'string', 'Property: href');
        $this->registerTagAttribute('type', 'string', 'Property: type');
        $this->registerTagAttribute('lang', 'string', 'Property: lang');
        $this->registerTagAttribute('dir', 'string', 'Property: dir');
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        if (ContextUtility::isBackend()) {
            return '';
        }
        static::getPageRenderer()->addHeaderData($this->renderTag($this->tagName));
        return '';
    }
}
