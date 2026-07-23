<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Case Formatting ViewHelper
 *
 * Formats string case according to provided arguments.
 */
class CaseViewHelper extends AbstractViewHelper
{
    private const string CASE_UPPER = 'upper';
    private const string CASE_LOWER = 'lower';
    private const string CASE_UCWORDS = 'ucwords';
    private const string CASE_UCFIRST = 'ucfirst';
    private const string CASE_LCFIRST = 'lcfirst';
    private const string CASE_CAMELCASE = 'CamelCase';
    private const string CASE_LOWERCAMELCASE = 'lowerCamelCase';
    private const string CASE_UNDERSCORED = 'lowercase_underscored';

    public function initializeArguments(): void
    {
        $this->registerArgument('string', 'string', 'String to case format');
        $this->registerArgument('case', 'string', 'Case to convert to');
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var string $string */
        $string = $arguments['string'] ?? $renderChildrenClosure();
        /** @var string $case */
        $case = $arguments['case'];

        $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();

        switch ($case) {
            case self::CASE_LOWER:
                $string = mb_strtolower($string);
                break;
            case self::CASE_UPPER:
                $string = mb_strtoupper($string);
                break;
            case self::CASE_UCWORDS:
                $string = ucwords($string);
                break;
            case self::CASE_UCFIRST:
                $firstChar = mb_substr($string, 0, 1);
                $firstChar = mb_strtoupper($firstChar);
                $remainder = mb_substr($string, 1, null);
                $string = $firstChar . $remainder;
                break;
            case self::CASE_LCFIRST:
                $firstChar = mb_substr($string, 0, 1);
                $firstChar = mb_strtolower($firstChar);
                $remainder = mb_substr($string, 1, null);
                $string = $firstChar . $remainder;
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

        FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);

        return $string;
    }
}
