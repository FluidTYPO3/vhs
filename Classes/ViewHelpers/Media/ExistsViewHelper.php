<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * File/Directory Exists Condition ViewHelper
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class ExistsViewHelper extends AbstractConditionViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('file', 'string', 'Filename which must exist to trigger f:then rendering', FALSE);
		$this->registerArgument('directory', 'string', 'Directory which must exist to trigger f:then rendering', FALSE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {

		$evaluation = static::evaluateCondition($this->arguments);

		if (FALSE !== $evaluation) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

	/**
	 * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
	 *
	 * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
	 * @return bool
	 */
	static protected function evaluateCondition($arguments = NULL) {
		$file = GeneralUtility::getFileAbsFileName($arguments['file']);
		$directory = $arguments['directory'];
		$evaluation = FALSE;
		if (TRUE === isset($arguments['file'])) {
			$evaluation = (boolean) ((TRUE === file_exists($file) || TRUE === file_exists(constant('PATH_site') . $file)) && TRUE === is_file($file));
		} elseif (TRUE === isset($arguments['directory'])) {
			$evaluation = (boolean) (TRUE === is_dir($directory) || TRUE === is_dir(constant('PATH_site') . $directory));
		}
		return $evaluation;
	}

}
