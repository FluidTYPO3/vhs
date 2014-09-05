<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Georg Ringer <typo3@ringerge.org>
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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper used to render a meta tag
 *
 * @author Georg Ringer
 * @package Vhs
 * @subpackage ViewHelpers\Page\Header
 */
class LinkViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var    string
	 */
	protected $tagName = 'link';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerTagAttribute('rel', 'string', 'Property: rel');
		$this->registerTagAttribute('href', 'string', 'Property: href');
		$this->registerTagAttribute('type', 'string', 'Property: type');
		$this->registerTagAttribute('lang', 'string', 'Property: lang');
		$this->registerTagAttribute('dir', 'string', 'Property: dir');
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
		$GLOBALS['TSFE']->getPageRenderer()->addMetaTag($this->tag->render());
	}

}
