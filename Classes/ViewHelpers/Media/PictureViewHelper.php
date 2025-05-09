<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TagViewHelperCompatibility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Renders a picture element with different images/sources for specific
 * media breakpoints
 *
 * ### Example
 *
 * ```
 * <v:media.picture src="fileadmin/some-image.png" alt="Some Image" loading="lazy">
 *     <v:media.source media="(min-width: 1200px)" width="500c" height="500c" />
 *     <v:media.source media="(min-width: 992px)" width="300c" height="300c" />
 *     <v:media.source media="(min-width: 768px)" width="200c" height="200c" />
 *     <v:media.source width="80c" height="80c" />
 * </v:media.picture>
 * ```
 *
 * ### Browser Support
 *
 * To have the widest Browser-Support you should consider using a polyfill like:
 * http://scottjehl.github.io/picturefill/
 */
class PictureViewHelper extends AbstractTagBasedViewHelper
{
    use TagViewHelperCompatibility;

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

    public function initializeArguments(): void
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
        $this->registerArgument(
            'loading',
            'string',
            'Native lazy-loading for images. Can be "lazy", "eager" or "auto"'
        );
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
        if ($src instanceof FileReference) {
            $src = $src->getUid();
            $treatIdAsReference = true;
        }

        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();
        $viewHelperVariableContainer->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_SRC, $src);
        $viewHelperVariableContainer->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_ID, $treatIdAsReference);
        $content = $this->renderChildren();
        $viewHelperVariableContainer->remove(static::SCOPE, static::SCOPE_VARIABLE_SRC);
        $viewHelperVariableContainer->remove(static::SCOPE, static::SCOPE_VARIABLE_ID);

        if (!$viewHelperVariableContainer->exists(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE)) {
            throw new Exception('Please add a source without a media query as a default.', 1438116616);
        }
        $defaultSource = $viewHelperVariableContainer->get(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE);

        /** @var string $alt */
        $alt = $this->arguments['alt'];

        $defaultImage = new TagBuilder('img');
        $defaultImage->addAttribute('src', is_scalar($defaultSource) ? (string) $defaultSource : '');
        $defaultImage->addAttribute('alt', $alt);

        /** @var string|null $class */
        $class = $this->arguments['class'];
        if (!empty($class)) {
            $defaultImage->addAttribute('class', $class);
        }

        /** @var string|null $title */
        $title = $this->arguments['title'];
        if (!empty($title)) {
            $defaultImage->addAttribute('title', $title);
        }

        /** @var string|null $loading */
        $loading = $this->arguments['loading'];
        if (in_array($loading ?? '', ['lazy', 'eager', 'auto'], true)) {
            $defaultImage->addAttribute('loading', $loading);
        }

        $content .= $defaultImage->render();

        $this->tag->setContent($content);
        return $this->tag->render();
    }
}
