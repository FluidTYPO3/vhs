<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Context;

/***************************************************************
 *  Copyright notice
 *  (c) 2014 Benjamin Beck <beck@beckdigitalemedien.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ### Context: Get
 *
 * Returns the current application context which may include possible sub-contexts.
 * The application context can be 'Production', 'Development' or 'Testing'.
 * Additionally each context can be extended with custom sub-contexts like: 'Production/Staging' or ' Production/Staging/Server1'.
 * If no application context has been set by the configuration, then the default context is 'Production'.
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
 * @subpackage ViewHelpers\Context
 */
class GetViewHelper extends AbstractViewHelper {

	/**
	 * @return string
	 */
	public function render () {
		return (string) GeneralUtility::getApplicationContext();
	}

}
