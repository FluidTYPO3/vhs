<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Returns random element from array
 *
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class RandomViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('as', 'string', 'Which variable to update in the TemplateVariableContainer. If left out, returns the random element instead of updating the variable', FALSE);
	}

	/**
	 * Render method
	 *
	 * @param mixed $subject
	 * @throws Exception
	 * @return mixed
	 */
	public function render($subject = NULL) {
		if (NULL === $subject && (FALSE === isset($as) || TRUE === empty($as))) {
			$subject = $this->renderChildren();
		}
		$as = $this->arguments['as'];
		$array = NULL;
		if (TRUE === is_array($subject)) {
			$array = $subject;
		} elseif (TRUE === $subject instanceof QueryResultInterface) {
			/** @var QueryResultInterface $subject */
			$array = $subject->toArray();
		} elseif (TRUE === $subject instanceof \Traversable) {
			$array = iterator_to_array($subject, TRUE);
		} elseif (NULL !== $subject) {
			throw new Exception('Invalid variable type passed to Iterator/RandomViewHelper. Expected any of Array, QueryResult, ' .
				' ObjectStorage or Iterator implementation but got ' . gettype($subject), 1370966821);
		} elseif (NULL === $subject) {
			return NULL;
		}
		$randomElement = $array[array_rand($array)];
		if (TRUE === isset($as) && FALSE === empty($as)) {
			$variables = array($as => $randomElement);
			$content = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
			return $content;
		}
		return $randomElement;
	}

}
