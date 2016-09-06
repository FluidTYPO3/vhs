<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Creates chunks from an input Array/Traversable with option to allocate items to a fixed number of chunks
 */
class ChunkViewHelper extends AbstractViewHelper
{

    use TemplateVariableViewHelperTrait;
    use ArrayConsumingViewHelperTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerAsArgument();
        $this->registerArgument('subject', 'mixed', 'The subject Traversable/Array instance to shift', false, null);
    }

    /**
     * Render method
     *
     * @param integer $count The count of items per chunk or if fixed number of chunks
     * @param boolean $fixed Whether to allocate items to a fixed number of chunks or not
     * @param boolean $preserveKeys If set to true, the original array keys will be preserved in the chunks
     * @throws \Exception
     * @return array
     */
    public function render($count, $fixed = false, $preserveKeys = false)
    {
        $subject = $this->getArgumentFromArgumentsOrTagContentAndConvertToArray('subject');
        $output = [];
        if (0 >= $count) {
            return $output;
        }
        if (true === (boolean) $fixed) {
            $subjectSize = count($subject);
            if (0 < $subjectSize) {
                $chunkSize = ceil($subjectSize / $count);
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
        return $this->renderChildrenWithVariableOrReturnInput($output);
    }
}
