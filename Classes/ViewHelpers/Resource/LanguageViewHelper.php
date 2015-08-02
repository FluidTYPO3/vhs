<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Resource: Language
 *
 * Reads a certain language file with returning not just one single label,
 * but all the translated labels.
 *
 * ### Examples
 *
 *    <!-- Tag usage for force getting labels in a specific language (different to current is possible too) -->
 *    <v:resource.language extensionName="myext" path="Path/To/Locallang.xlf" languageKey="en"/>
 *
 *    <!-- Tag usage for getting labels of current language -->
 *    <v:resource.language extensionName="myext" path="Path/To/Locallang.xlf"/>
 *
 * @author Cornel Boppart <cornel@bopp-art.com>
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
class LanguageViewHelper extends AbstractViewHelper {

	use TemplateVariableViewHelperTrait;

	const LOCALLANG_DEFAULT = 'locallang.xlf';

	/**
	 * Registers all arguments for this ViewHelper.
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerAsArgument();
		$this->registerArgument('extensionName', 'string', 'Name of the extension', FALSE, NULL);
		$this->registerArgument('path', 'string', 'Absolute or relative path to the locallang file', FALSE, self::LOCALLANG_DEFAULT);
		$this->registerArgument('languageKey', 'string', 'Key for getting translation of a different than current initialized language', FALSE, NULL);
	}

	/**
	 * The main render method of this ViewHelper.
	 *
	 * @return array|string
	 */
	public function render() {
		$path = $this->getResolvedPath();
		$languageKey = $this->getLanguageKey();
		$locallang = GeneralUtility::readLLfile($path, $languageKey);
		$labels = $this->getLabelsByLanguageKey($locallang, $languageKey);
		$labels = $this->getLabelsFromTarget($labels);
		return $this->renderChildrenWithVariableOrReturnInput($labels);
	}

	/**
	 * Gets the extension name from defined argument or
	 * tries to resolve it from the controller context if not set.
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function getResolvedExtensionName() {
		$extensionName = $this->arguments['extensionName'];

		if ((NULL === $extensionName) && (TRUE === $this->controllerContext instanceof ControllerContext)) {
			$request = $this->controllerContext->getRequest();
			$extensionName = $request->getControllerExtensionName();
		}

		if (TRUE === empty($extensionName)) {
			throw new Exception('Unable to read extension name from ControllerContext and value not manually specified');
		}

		return $extensionName;
	}

	/**
	 * Gets the resolved file path with trying to resolve relative paths even if no
	 * extension key is defined.
	 *
	 * @return string
	 */
	protected function getResolvedPath() {
		$path = $this->arguments['path'];
		$absoluteFileName = GeneralUtility::getFileAbsFileName($this->arguments['path']);

		if (FALSE === file_exists($absoluteFileName)) {
			$extensionName = $this->getResolvedExtensionName();
			$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
			$absoluteFileName = ExtensionManagementUtility::extPath($extensionKey, $path);
		}

		return $absoluteFileName;
	}

	/**
	 * Gets the translated labels by a specific language key
	 * or fallback to 'default'.
	 *
	 * @param array $locallang
	 * @param string $languageKey
	 * @return array
	 */
	protected function getLabelsByLanguageKey($locallang, $languageKey) {
		$labels = array();

		if (FALSE === empty($locallang[$languageKey])) {
			$labels = $locallang[$languageKey];
		} elseif (FALSE === empty($locallang['default'])) {
			$labels = $locallang['default'];
		}

		return $labels;
	}

	/**
	 * Simplify label array with just taking the value from target.
	 *
	 * @param array $labels
	 * @return array
	 */
	protected function getLabelsFromTarget($labels) {
		if (TRUE === is_array($labels)) {
			foreach ($labels as $labelKey => $label) {
				$labels[$labelKey] = $label[0]['target'];
			}
		}

		return $labels;
	}

	/**
	 * Gets the language key from arguments or from current
	 * initialized language if argument is not defined.
	 *
	 * @return string
	 */
	protected function getLanguageKey() {
		$languageKey = $this->arguments['languageKey'];

		if (NULL === $languageKey) {
			$languageKey = $this->getInitializedLanguage();
		}

		return $languageKey;
	}

	/**
	 * Gets the key of current initialized language
	 * or fallback to 'default'.
	 *
	 * @return string
	 */
	protected function getInitializedLanguage() {
		$language = 'default';

		if ('FE' === TYPO3_MODE) {
			$language = $GLOBALS['TSFE']->lang;
		} elseif (TRUE === is_object($GLOBALS['LANG'])) {
			$language = $GLOBALS['LANG']->lang;
		}

		return $language;
	}

}
