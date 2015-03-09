<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Context;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Context: IsDevelopment
 *
 * Returns true if current root application context is development otherwise false.
 * If no application context has been set, then the default context is production.
 *
 * #### Note about how to set the application context
 *
 * The context TYPO3 CMS runs in is specified through the environment variable TYPO3_CONTEXT.
 * It can be set by .htaccess or in the server configuration
 *
 * See: http://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Bootstrapping/Index.html#bootstrapping-context
 *
 * @author     Benjamin Beck <beck@beckdigitalemedien.de>
 * @package    Vhs
 * @subpackage ViewHelpers\Condition\Context
 */
class IsDevelopmentViewHelper extends AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render () {
		return (TRUE === $this->isDevelopmentContext() ? $this->renderThenChild() : $this->renderElseChild());
	}


	/**
	 * @return boolean
	 */
	protected function isDevelopmentContext () {
		return GeneralUtility::getApplicationContext()->isDevelopment();
	}
}
