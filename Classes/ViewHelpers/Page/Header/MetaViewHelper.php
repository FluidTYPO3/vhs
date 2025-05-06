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
 * ViewHelper used to render a meta tag
 *
 * If you use the ViewHelper in a plugin it has to be USER
 * not USER_INT, what means it has to be cached!
 */
class MetaViewHelper extends AbstractTagBasedViewHelper
{
    use TagViewHelperTrait;
    use PageRendererTrait;

    /**
     * @var string
     */
    protected $tagName = 'meta';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerTagAttribute('name', 'string', 'Name property of meta tag');
        $this->registerTagAttribute('http-equiv', 'string', 'Property: http-equiv');
        $this->registerTagAttribute('property', 'string', 'Property of meta tag');
        $this->registerTagAttribute('content', 'string', 'Content of meta tag');
        $this->registerTagAttribute('scheme', 'string', 'Property: scheme');
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
        /** @var string|null $content */
        $content = $this->arguments['content'];
        if (!empty($content)) {
            $pageRenderer = static::getPageRenderer();
            if (method_exists($pageRenderer, 'addMetaTag')) {
                $pageRenderer->addMetaTag($this->renderTag($this->tagName, null, ['content' => $content]));
            } elseif (method_exists($pageRenderer, 'setMetaTag')) {
                $properties = [];
                $type = 'name';
                /** @var string $name */
                $name = $this->tag->getAttribute('name') ?? $this->arguments['name'];
                if (!empty($this->tag->getAttribute('property') ?? $this->arguments['property'] ?? null)) {
                    $type = 'property';
                    /** @var string $name */
                    $name = $this->tag->getAttribute('property') ?? $this->arguments['property'];
                } elseif (!empty($this->tag->getAttribute('http-equiv') ?? $this->arguments['http-equiv'] ?? null)) {
                    $type = 'http-equiv';
                    /** @var string $name */
                    $name = $this->tag->getAttribute('http-equiv') ?? $this->arguments['http-equiv'];
                }
                foreach (['http-equiv', 'property', 'scheme', 'lang', 'dir'] as $propertyName) {
                    if (!empty($this->tag->getAttribute($propertyName) ?? $this->arguments[$propertyName] ?? null)) {
                        $properties[$propertyName] = $this->tag->getAttribute($propertyName)
                            ?? $this->arguments[$propertyName]
                            ?? null;
                    }
                }
                $pageRenderer->setMetaTag($type, $name, $content, $properties);
            }
        }
        return '';
    }
}
