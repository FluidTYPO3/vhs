<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;

/**
 * ### Condition: Is current language
 *
 * A condition ViewHelper which renders the `then` child if
 * current language matches the provided language uid or language
 * title. When using language titles like 'de' it is required to
 * provide a default title to distinguish between the standard
 * and a non existing language.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Page
 */
class IsLanguageViewHelper extends AbstractConditionViewHelper {

	use ConditionViewHelperTrait;

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('language', 'string', 'language to check', TRUE);
		$this->registerArgument('defaultTitle', 'string', 'title of the default language', FALSE, 'en');
	}

	/**
	 * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
	 *
	 * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
	 * @return bool
	 */
	static protected function evaluateCondition($arguments = NULL) {
		$language = $arguments['language'];
		$defaultTitle = $arguments['defaultTitle'];

		$currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
		if (TRUE === is_numeric($language)) {
			$languageUid = intval($language);
		} else {
			$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid', 'sys_language', "title='" . $language . "'");
			if (FALSE !== $row) {
				$languageUid = intval($row['uid']);
			} else {
				if ((string) $language === $defaultTitle) {
					$languageUid = $currentLanguageUid;
				} else {
					$languageUid = -1;
				}
			}
		}
		return $languageUid === $currentLanguageUid;
	}

}
