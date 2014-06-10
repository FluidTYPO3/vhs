<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due, Wildside A/S <claus@wildside.dk>
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

/**
 * ### ViewHelper Debug ViewHelper (sic)
 *
 * @package Vhs
 * @subpackage ViewHelpers
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class DebugViewHelper extends AbstractViewHelper implements ChildNodeAccessInterface {

	/**
	 * @var \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode[]
	 */
	protected $childViewHelperNodes = array();

	/**
	 * @return mixed
	 */
	public function render() {
		$nodes = array();
		foreach ($this->childViewHelperNodes as $viewHelperNode) {
			$viewHelper = $viewHelperNode->getUninitializedViewHelper();
			$arguments = $viewHelper->prepareArguments();
			$givenArguments = $viewHelperNode->getArguments();
			$viewHelperReflection = new \ReflectionClass($viewHelper);
			$viewHelperDescription = $viewHelperReflection->getDocComment();
			$viewHelperDescription = htmlentities($viewHelperDescription);
			$viewHelperDescription = '[CLASS DOC]' . LF . $viewHelperDescription . LF;
			$renderMethodDescription = $viewHelperReflection->getMethod('render')->getDocComment();
			$renderMethodDescription = htmlentities($renderMethodDescription);
			$renderMethodDescription = implode(LF, array_map('trim', explode(LF, $renderMethodDescription)));
			$renderMethodDescription = '[RENDER METHOD DOC]' . LF . $renderMethodDescription . LF;
			$argumentDefinitions = array();
			foreach ($arguments as &$argument) {
				$name = $argument->getName();
				$argumentDefinitions[$name] = ObjectAccess::getGettableProperties($argument);
			}
			$sections = array(
				$viewHelperDescription,
				DebuggerUtility::var_dump($argumentDefinitions, '[ARGUMENTS]', 4, TRUE, FALSE, TRUE),
				DebuggerUtility::var_dump($givenArguments, '[CURRENT ARGUMENTS]', 4, TRUE, FALSE, TRUE),
				$renderMethodDescription
			);
			array_push($nodes, implode(LF, $sections));

		}
		return '<pre>' . implode(LF . LF, $nodes) . '</pre>';
	}

	/**
	 * Sets the direct child nodes of the current syntax tree node.
	 *
	 * @param \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\AbstractNode[] $childNodes
	 * @return void
	 */
	public function setChildNodes(array $childNodes) {
		foreach ($childNodes as $childNode) {
			if (TRUE === $childNode instanceof \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode) {
				array_push($this->childViewHelperNodes, $childNode);
			}
		}
	}

}
