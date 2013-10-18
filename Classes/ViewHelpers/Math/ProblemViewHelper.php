<?php

class Tx_Vhs_ViewHelpers_Math_ProblemViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {
    /**
     * @param string $a
     * 
     * @return string
     */
	public function render($a){
		return( \TYPO3\CMS\Core\Utility\MathUtility::calculateWithParentheses($a) );
	}
	
}

