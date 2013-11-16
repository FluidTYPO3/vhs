<?php


class Tx_Vhs_ViewHelpers_Math_ProblemViewHelper extends Tx_Vhs_ViewHelpers_Math_AbstractMultipleMathViewHelper {
    /**
     * @param string problem
     * 
     * @return string
     */
	public function render($problem){
		return( \TYPO3\CMS\Core\Utility\MathUtility::calculateWithParentheses($problem) );
	} 
	
}

