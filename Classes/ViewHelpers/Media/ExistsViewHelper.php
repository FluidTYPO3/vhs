<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\CoreUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * File/Directory Exists Condition ViewHelper.
 */
class ExistsViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('file', 'string', 'Filename which must exist to trigger f:then rendering');
        $this->registerArgument('directory', 'string', 'Directory which must exist to trigger f:then rendering');
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        /** @var string $file */
        $file = $arguments['file'];
        $file = GeneralUtility::getFileAbsFileName((string) $file);
        /** @var string $directory */
        $directory = $arguments['directory'];
        $evaluation = false;
        if (isset($arguments['file'])) {
            $evaluation = ((file_exists($file) || file_exists(CoreUtility::getSitePath() . $file)) && is_file($file));
        } elseif (isset($arguments['directory'])) {
            $evaluation = (is_dir($directory) || is_dir(CoreUtility::getSitePath() . $directory));
        }
        return $evaluation;
    }
}
