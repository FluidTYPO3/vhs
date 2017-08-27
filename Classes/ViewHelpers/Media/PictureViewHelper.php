<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Renders a picture element with different images/sources for specific
 * media breakpoints
 *
 * ### Example
 *
 *     <v:media.picture src="fileadmin/some-image.png" alt="Some Image">
 *         <v:media.source media="(min-width: 1200px)" width="500c" height="500c" />
 *         <v:media.source media="(min-width: 992px)" width="300c" height="300c" />
 *         <v:media.source media="(min-width: 768px)" width="200c" height="200c" />
 *         <v:media.source width="80c" height="80c" />
 *     </v:media.picture>
 *
 * ### Browser Support
 *
 * To have the widest Browser-Support you should consider using a polyfill like:
 * http://scottjehl.github.io/picturefill/
 */
class PictureViewHelper extends AbstractTagBasedViewHelper
{
    const SCOPE = 'FluidTYPO3\Vhs\ViewHelpers\Media\PictureViewHelper';
    const SCOPE_VARIABLE_SRC = 'src';
    const SCOPE_VARIABLE_ID = 'treatIdAsReference';
    const SCOPE_VARIABLE_DEFAULT_SOURCE = 'default-source';

    /**
     * name of the tag to be created by this view helper
     *
     * @var string
     * @api
     */
    protected $tagName = 'picture';

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('src', 'mixed', 'Path to the image or FileReference.', true);
        $this->registerArgument(
            'treatIdAsReference',
            'boolean',
            'When TRUE treat given src argument as sys_file_reference record.',
            false,
            false
        );
        $this->registerArgument('alt', 'string', 'Text for the alt attribute.', true);
        $this->registerArgument('title', 'string', 'Text for the title attribute.');
        $this->registerArgument('class', 'string', 'CSS class(es) to set.');
    }

    /**
     * Render method
     * @return string
     * @throws Exception
     */
    public function render()
    {
        $src = $this->arguments['src'];
        $treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];
        if (is_object($src) && $src instanceof FileReference) {
            $src = $src->getUid();
            $treatIdAsReference = true;
        }

        $this->viewHelperVariableContainer->addOrUpdate(self::SCOPE, self::SCOPE_VARIABLE_SRC, $src);
        $this->viewHelperVariableContainer->addOrUpdate(self::SCOPE, self::SCOPE_VARIABLE_ID, $treatIdAsReference);
        $content = $this->renderChildren();
        $this->viewHelperVariableContainer->remove(self::SCOPE, self::SCOPE_VARIABLE_SRC);
        $this->viewHelperVariableContainer->remove(self::SCOPE, self::SCOPE_VARIABLE_ID);

        if (false === $this->viewHelperVariableContainer->exists(self::SCOPE, self::SCOPE_VARIABLE_DEFAULT_SOURCE)) {
            throw new Exception('Please add a source without a media query as a default.', 1438116616);
        }
        $defaultSource = $this->viewHelperVariableContainer->get(self::SCOPE, self::SCOPE_VARIABLE_DEFAULT_SOURCE);

        $defaultImage = new TagBuilder('img');
        $defaultImage->addAttribute('src', $defaultSource);
        $defaultImage->addAttribute('alt', $this->arguments['alt']);

        if (false === empty($this->arguments['title'])) {
            $defaultImage->addAttribute('title', $this->arguments['title']);
        }
        $content .= $defaultImage->render();

        $this->tag->setContent($content);
        if (false === empty($this->arguments['class'])) {
            $this->tag->addAttribute('class', $this->arguments['class']);
        }
        return $this->tag->render();
    }
}
