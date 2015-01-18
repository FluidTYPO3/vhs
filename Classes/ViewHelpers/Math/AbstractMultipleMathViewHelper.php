<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Base class: Math ViewHelpers operating on one number or an
 * array of numbers.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers
 */
abstract class AbstractMultipleMathViewHelper extends AbstractSingleMathViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('b', 'mixed', 'Second number or Iterator/Traversable/Array for calculation', TRUE);
	}

	/**
	 * @return mixed
	 * @throw \Exception
	 */
	public function render() {
		$a = $this->getInlineArgument();
		$b = $this->arguments['b'];
		return $this->calculate($a, $b);
	}

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return mixed
	 * @throws \Exception
	 */
	protected function calculate($a, $b) {
		if ($b === NULL) {
			throw new \Exception('Required argument "b" was not supplied', 1237823699);
		}
		$aIsIterable = $this->assertIsArrayOrIterator($a);
		$bIsIterable = $this->assertIsArrayOrIterator($b);
		if (TRUE === $aIsIterable) {
			$aCanBeAccessed = $this->assertSupportsArrayAccess($a);
			$bCanBeAccessed = $this->assertSupportsArrayAccess($b);
			if (FALSE === $aCanBeAccessed || (TRUE === $bIsIterable && FALSE === $bCanBeAccessed)) {
				throw new \Exception('Math operation attempted on an inaccessible Iterator. Please implement ArrayAccess or convert the value to an array before calculation', 1351891091);
			}
			foreach ($a as $index => $value) {
				$bSideValue = TRUE === $bIsIterable ? $b[$index] : $b;
				$a[$index] = $this->calculateAction($value, $bSideValue);
			}
			return $a;
		} elseif (TRUE === $bIsIterable) {
			// condition matched if $a is not iterable but $b is.
			throw new \Exception('Math operation attempted using an iterator $b against a numeric value $a. Either both $a and $b, or only $a, must be array/Iterator', 1351890876);
		}
		return $this->calculateAction($a, $b);
	}

}
