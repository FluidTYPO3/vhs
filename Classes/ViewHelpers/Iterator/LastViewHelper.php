<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Returns the last element of $haystack
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class LastViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @throws \Exception
	 * @return mixed|NULL
	 */
	public function render() {
		$haystack = $this->arguments['haystack'];
		if (NULL === $haystack) {
			$haystack = $this->renderChildren();
		}
		if (FALSE === is_array($haystack) && FALSE === $haystack instanceof \Iterator && FALSE === is_null($haystack)) {
			throw new Exception('Invalid argument supplied to Iterator/LastViewHelper - expected array, Iterator or NULL but got ' .
				gettype($haystack), 1351958398);
		}
		if (NULL === $haystack) {
			return NULL;
		}
		$needle = NULL;
		foreach ($haystack as $needle) {
			// do nothing; but use foreach in order to a) reset pointer
			// before iteration and b) make sure any Iterator/array is
			// supported without the need for any special tricks. The
			// isset() call is here only to prevent code style violations
			isset($needle);
		}
		return $needle;
	}

}
