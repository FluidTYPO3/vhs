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
 * If you use the ViewHelper in a plugin it has to be USER
 * not USER_INT, what means it has to be cached!
 *
 * @author Georg Ringer
 * @package Vhs
 * @subpackage ViewHelpers\Page\Header
 */
class MetaViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var    string
	 */
	protected $tagName = 'meta';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerTagAttribute('name', 'string', 'Name property of meta tag');
		$this->registerTagAttribute('content', 'string', 'Content of meta tag');
		$this->registerTagAttribute('http-equiv', 'string', 'Property: http-equiv');
		$this->registerTagAttribute('scheme', 'string', 'Property: scheme');
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
		if (TRUE === isset($this->arguments['content']) && FALSE === empty($this->arguments['content'])) {
			$GLOBALS['TSFE']->getPageRenderer()->addMetaTag($this->tag->render());
		}
	}

}
