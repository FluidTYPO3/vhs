<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
