<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TagViewHelperTrait;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ### Tag building ViewHelper
 *
 * Creates one HTML tag of any type, with various properties
 * like class and ID applied only if arguments are not empty,
 * rather than apply them always - empty or not - if provided.
 */
class TagViewHelper extends AbstractTagBasedViewHelper
{
    use TagViewHelperTrait;

    public function initializeArguments(): void
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
        /** @var string|null $class */
        $class = $this->arguments['class'] ?? null;
        $class = trim((string) (is_scalar($class) ? $class : null));
        $class = str_replace(',', ' ', $class);

        $this->arguments['class'] = $class;
        /** @var string $tagName */
        $tagName = $this->arguments['name'];
        /** @var string $content */
        $content = $this->renderChildren();
        return $this->renderTag($tagName, $content);
    }
}
