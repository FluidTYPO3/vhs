<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Creates chunks from an input Array/Traversable with option to allocate items to a fixed number of chunks
 */
class ChunkViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;
    use ArrayConsumingViewHelperTrait;
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('subject', 'mixed', 'The subject Traversable/Array instance to shift');
        $this->registerArgument('count', 'integer', 'Number of items/chunk or if fixed then number of chunks', true);
        $this->registerAsArgument();
        $this->registerArgument(
            'fixed',
            'boolean',
            'If true, creates $count chunks instead of $count values per chunk',
            false,
            false
        );
        $this->registerArgument(
            'preserveKeys',
            'boolean',
            'If set to true, the original array keys will be preserved',
            false,
            false
        );
    }

    /**
     * @return array|mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var string|null $as */
        $as = $arguments['as'];
        /** @var int $count */
        $count = $arguments['count'];
        $fixed = (boolean) $arguments['fixed'];
        $preserveKeys = (boolean) $arguments['preserveKeys'];
        $subject = static::arrayFromArrayOrTraversableOrCSVStatic(
            empty($as) ? ($arguments['subject'] ?? $renderChildrenClosure()) : $arguments['subject'],
            $preserveKeys
        );
        $output = [];
        if (0 >= $count) {
            return $output;
        }
        if ($fixed) {
            $subjectSize = count($subject);
            if (0 < $subjectSize) {
                /** @var int<1, max> $chunkSize */
                $chunkSize = (integer) ceil($subjectSize / $count);
                $output = array_chunk($subject, $chunkSize, $preserveKeys);
            }
            // Fill the resulting array with empty items to get the desired element count
            $elementCount = count($output);
            if ($elementCount < $count) {
                $output += array_fill($elementCount, $count - $elementCount, null);
            }
        } else {
            $output = array_chunk($subject, $count, $preserveKeys);
        }

        return static::renderChildrenWithVariableOrReturnInputStatic(
            $output,
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
