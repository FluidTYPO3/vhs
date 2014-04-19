<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Form;

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
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;

/**
 * ### Is Field Required ViewHelper (condition)
 *
 * Takes a property (dotted path supported) and renders the
 * then-child if the property at the given path has an
 * @validate NotEmpty annotation
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Form
 */
class IsRequiredViewHelper extends HasValidatorViewHelper {

	/**
	 * Render
	 *
	 * Renders the then-child if the property at $property of the
	 * object at $object (or the associated form object if $object
	 * is not specified)
	 *
	 * @param string $property The property name, dotted path supported, to determine required
	 * @param DomainObjectInterface $object Optional object - if not specified, grabs the associated form object
	 * @return string
	 */
	public function render($property, DomainObjectInterface $object = NULL) {
		$validatorName = 'NotEmpty';
		return parent::render($property, $validatorName, $object);
	}

}
