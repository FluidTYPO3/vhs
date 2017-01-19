<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Case Formatting ViewHelper
 *
 * Formats string case according to provided arguments.
 */
class CaseViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    const CASE_UPPER = 'upper';
    const CASE_LOWER = 'lower';
    const CASE_UCWORDS = 'ucwords';
    const CASE_UCFIRST = 'ucfirst';
    const CASE_LCFIRST = 'lcfirst';
    const CASE_CAMELCASE = 'CamelCase';
    const CASE_LOWERCAMELCASE = 'lowerCamelCase';
    const CASE_UNDERSCORED = 'lowercase_underscored';

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('string', 'string', 'String to case format');
        $this->registerArgument('case', 'string', 'Case to convert to');
    }


    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $string = $renderChildrenClosure();
        $case = $arguments['case'];

        if ('BE' === TYPO3_MODE) {
            $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();
        }
        $charset = $GLOBALS['TSFE']->renderCharset;
        switch ($case) {
            case self::CASE_LOWER:
                $string = $GLOBALS['TSFE']->csConvObj->conv_case($charset, $string, 'toLower');
                break;
            case self::CASE_UPPER:
                $string = $GLOBALS['TSFE']->csConvObj->conv_case($charset, $string, 'toUpper');
                break;
            case self::CASE_UCWORDS:
                $string = ucwords($string);
                break;
            case self::CASE_UCFIRST:
                $string = $GLOBALS['TSFE']->csConvObj->convCaseFirst($charset, $string, 'toUpper');
                break;
            case self::CASE_LCFIRST:
                $string = $GLOBALS['TSFE']->csConvObj->convCaseFirst($charset, $string, 'toLower');
                break;
            case self::CASE_CAMELCASE:
                $string = GeneralUtility::underscoredToUpperCamelCase($string);
                break;
            case self::CASE_LOWERCAMELCASE:
                $string = GeneralUtility::underscoredToLowerCamelCase($string);
                break;
            case self::CASE_UNDERSCORED:
                $string = GeneralUtility::camelCaseToLowerCaseUnderscored($string);
                break;
            default:
                break;
        }
        if ('BE' === TYPO3_MODE) {
            FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);
        }
        return $string;
    }
}
