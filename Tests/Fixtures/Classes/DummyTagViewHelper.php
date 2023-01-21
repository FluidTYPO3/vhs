<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use FluidTYPO3\Vhs\Traits\TagViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class DummyTagViewHelper extends AbstractTagBasedViewHelper
{
    use TagViewHelperTrait;

    public $arguments = [];

    /**
     * @var TagBuilder
     */
    public $tag;

    public function testRenderTag(
        string $tagName,
        ?string $content = null,
        array $attributes = [],
        array $nonEmptyAttributes = ['id', 'class']
    ): string {
        return $this->renderTag($tagName, $content, $attributes, $nonEmptyAttributes);
    }

    public function testRenderChildTag(
        string $tagName,
        array $attributes = [],
        bool $forceClosingTag = false,
        string $mode = 'none'
    ): string {
        $this->renderChildTag($tagName, $attributes, $forceClosingTag, $mode);
        return $this->tag->render();
    }
}
