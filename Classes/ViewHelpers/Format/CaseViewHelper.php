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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
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
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $string = $renderChildrenClosure();
        $case = $arguments['case'];

        $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();

        switch ($case) {
            case static::CASE_LOWER:
                $string = mb_strtolower($string);
                break;
            case static::CASE_UPPER:
                $string = mb_strtoupper($string);
                break;
            case static::CASE_UCWORDS:
                $string = ucwords($string);
                break;
            case static::CASE_UCFIRST:
                $firstChar = mb_substr($string, 0, 1);
                $firstChar = mb_strtoupper($firstChar);
                $remainder = mb_substr($string, 1, null);
                $string = $firstChar . $remainder;
                break;
            case static::CASE_LCFIRST:
                $firstChar = mb_substr($string, 0, 1);
                $firstChar = mb_strtolower($firstChar);
                $remainder = mb_substr($string, 1, null);
                $string = $firstChar . $remainder;
                break;
            case static::CASE_CAMELCASE:
                $string = GeneralUtility::underscoredToUpperCamelCase($string);
                break;
            case static::CASE_LOWERCAMELCASE:
                $string = GeneralUtility::underscoredToLowerCamelCase($string);
                break;
            case static::CASE_UNDERSCORED:
                $string = GeneralUtility::camelCaseToLowerCaseUnderscored($string);
                break;
            default:
                break;
        }

        FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);

        return $string;
    }
}
