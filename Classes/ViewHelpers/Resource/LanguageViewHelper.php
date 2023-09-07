<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Resource: Language
 *
 * Reads a certain language file with returning not just one single label,
 * but all the translated labels.
 *
 * ### Examples
 *
 * ```
 * <!-- Tag usage for force getting labels in a specific language (different to current is possible too) -->
 * <v:resource.language extensionName="myext" path="Path/To/Locallang.xlf" languageKey="en"/>
 * ```
 *
 * ```
 * <!-- Tag usage for getting labels of current language -->
 * <v:resource.language extensionName="myext" path="Path/To/Locallang.xlf"/>
 * ```
 */
class LanguageViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;

    const LOCALLANG_DEFAULT = 'locallang.xlf';

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerAsArgument();
        $this->registerArgument('extensionName', 'string', 'Name of the extension');
        $this->registerArgument(
            'path',
            'string',
            'Absolute or relative path to the locallang file',
            false,
            static::LOCALLANG_DEFAULT
        );
        $this->registerArgument(
            'languageKey',
            'string',
            'Key for getting translation of a different than current initialized language'
        );
    }

    /**
     * The main render method of this ViewHelper.
     *
     * @return mixed
     */
    public function render()
    {
        $path = $this->getResolvedPath();
        $languageKey = $this->getLanguageKey();
        /** @var LocalizationFactory $languageFactory */
        $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
        $locallang = (array) $languageFactory->getParsedData($path, $languageKey);
        $labels = $this->getLabelsByLanguageKey($locallang, $languageKey);
        $labels = $this->getLabelsFromTarget($labels);
        return $this->renderChildrenWithVariableOrReturnInput($labels);
    }

    /**
     * Gets the extension name from defined argument or
     * tries to resolve it from the controller context if not set.
     */
    protected function getResolvedExtensionName(): string
    {
        /** @var string|null $extensionName */
        $extensionName = $this->arguments['extensionName'];

        return $extensionName ?? RequestResolver::resolveRequestFromRenderingContext($this->renderingContext)
            ->getControllerExtensionName();
    }

    /**
     * Gets the resolved file path with trying to resolve relative paths even if no
     * extension key is defined.
     */
    protected function getResolvedPath(): string
    {
        /** @var string $path */
        $path = $this->arguments['path'];
        $absoluteFileName = GeneralUtility::getFileAbsFileName($path);

        if (!file_exists($absoluteFileName)) {
            $extensionName = $this->getResolvedExtensionName();
            $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
            $absoluteFileName = ExtensionManagementUtility::extPath($extensionKey, $path);
        }

        return $absoluteFileName;
    }

    /**
     * Gets the translated labels by a specific language key
     * or fallback to 'default'.
     */
    protected function getLabelsByLanguageKey(array $locallang, string $languageKey): array
    {
        $labels = [];

        if (!empty($locallang[$languageKey])) {
            $labels = $locallang[$languageKey];
        } elseif (!empty($locallang['default'])) {
            $labels = $locallang['default'];
        }

        return $labels;
    }

    /**
     * Simplify label array with just taking the value from target.
     */
    protected function getLabelsFromTarget(array $labels): array
    {
        foreach ($labels as $labelKey => $label) {
            $labels[$labelKey] = $label[0]['target'];
        }

        return $labels;
    }

    /**
     * Gets the language key from arguments or from current
     * initialized language if argument is not defined.
     */
    protected function getLanguageKey(): string
    {
        /** @var string|null $languageKey */
        $languageKey = $this->arguments['languageKey'];
        return $languageKey ?? $this->getInitializedLanguage();
    }

    /**
     * Gets the key of current initialized language
     * or fallback to 'default'.
     */
    protected function getInitializedLanguage(): string
    {
        $language = 'default';

        if (ContextUtility::isFrontend()) {
            $language = $GLOBALS['TSFE']->lang;
        } elseif ($GLOBALS['LANG'] instanceof LanguageService) {
            $language = $GLOBALS['LANG']->lang;
        }

        return (string) $language;
    }
}
