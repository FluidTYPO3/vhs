<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### ViewHelper used to override page title
 *
 * This ViewHelper uses the TYPO3 PageRenderer to set the
 * page title - with everything this implies regarding
 * support for TypoScript settings.
 *
 * Specifically you should note the setting `config.noPageTitle`
 * which must be set to either 1 (one) in case no other source
 * defines the page title (it's likely that at least one does),
 * or 2 (two) to indicate that the TS-controlled page title
 * must be disabled. A value of 2 (two) ensures that the title
 * used in this ViewHelper will be used in the rendered page.
 *
 * If you use the ViewHelper in a plugin it has to be USER
 * not USER_INT, what means it has to be cached!
 *
 * #### Why can I not forcibly override the title?
 *
 * This has been opted out with full intention. The reasoning
 * behind not allowing a Fluid template to forcibly override the
 * page title that may be set through TypoScript is that many
 * other extensions (mainly SEO-focused ones) will be setting
 * and manipulating the page title - and if overridden in a
 * template file using a ViewHelper, it would be almost impossible
 * to detect unless you already know exactly where to look.
 * Enforcing use of the core behavior is the only way to ensure
 * that this ViewHelper can coexist with other extensions in
 * a fully controllable way.
 *
 * @author Georg Ringer
 * @package Vhs
 * @subpackage ViewHelpers\Page\Header
 */
class TitleViewHelper extends AbstractViewHelper {

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('title', 'string', 'Title tag content');
		$this->registerArgument('whitespaceString', 'string', 'String used to replace groups of white space characters, one replacement inserted per group', FALSE, ' ');
		$this->registerArgument('setIndexedDocTitle', 'boolean', 'Set indexed doc title to title', FALSE, FALSE);
	}

	/**
	 * Render method
	 *
	 * @return void
	 */
	public function render() {
		if ('BE' === TYPO3_MODE) {
			return;
		}
		if (FALSE === empty($this->arguments['title'])) {
			$title = $this->arguments['title'];
		} else {
			$title = $this->renderChildren();
		}
		$title = trim(preg_replace('/\s+/', $this->arguments['whitespaceString'], $title), $this->arguments['whitespaceString']);
		$GLOBALS['TSFE']->getPageRenderer()->setTitle($title);
		if (TRUE === $this->arguments['setIndexedDocTitle']) {
			$GLOBALS['TSFE']->indexedDocTitle = $title;
		}
	}

}
