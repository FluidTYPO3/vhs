<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;

/**
 * ### Switch ViewHelper
 *
 * Fluid implementation of PHP's switch($value) construct.
 *
 * ### Example
 *
 *     <v:switch value="{variable}">
 *         <v:case case="someValue" break="TRUE">
 *             <!-- do whatever, if {variable} == 'someValue' -->
 *         </v:case>
 *         <v:case case="default">
 *             <!-- the case "default" is a reserved keyword which acts as the default case. -->
 *         </v:case>
 *     </v:switch>
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers
 */
class SwitchViewHelper extends AbstractViewHelper implements ChildNodeAccessInterface {

	/**
	 * @var array
	 */
	private $childNodes = array();

	/**
	 * @var mixed
	 */
	private $backup;

	/**
	 * Initialize
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('value', 'string', 'Variable on which to switch - string, integer or number', TRUE);
		$this->registerArgument('as', 'string', 'If specified, inserts the matched case tag content as variable using name from "as"');
	}

	/**
	 * @param array $childNodes
	 * @return void
	 */
	public function setChildNodes(array $childNodes) {
		$this->childNodes = $childNodes;
	}

	/**
	 * Renders the case in the switch which matches variable, else default case
	 * @return string
	 */
	public function render() {
		$content = '';
		if (TRUE === method_exists($this, 'getRenderingContext')) {
			$context = $this->getRenderingContext();
		} else {
			$context = $this->renderingContext;
		}
		if (TRUE === $context->getViewHelperVariableContainer()->exists('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchCaseValue')) {
			$this->storeBackup($context);
		}
		$context->getViewHelperVariableContainer()->addOrUpdate('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchCaseValue', $this->arguments['value']);
		$context->getViewHelperVariableContainer()->addOrUpdate('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchBreakRequested', FALSE);
		$context->getViewHelperVariableContainer()->addOrUpdate('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchContinueUntilBreak', FALSE);
		foreach ($this->childNodes as $childNode) {
			if (TRUE === $childNode instanceof ViewHelperNode
				&& $childNode->getViewHelperClassName() === 'FluidTYPO3\Vhs\ViewHelpers\CaseViewHelper') {
				$content .= $childNode->evaluate($context);
				$shouldBreak = $this->determineBooleanOf($context, 'switchBreakRequested');
				if (TRUE === $shouldBreak) {
					return $content;
				}
			}
		}
		$context->getViewHelperVariableContainer()->remove('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchCaseValue');
		$context->getViewHelperVariableContainer()->remove('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchBreakRequested');
		$context->getViewHelperVariableContainer()->remove('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchContinueUntilBreak');
		if (NULL !== $this->backup) {
			$this->restoreBackup($context);
		}
		if (TRUE === isset($this->arguments['as'])) {
			$this->templateVariableContainer->add($this->arguments['as'], $content);
			return NULL;
		}
		return $content;
	}

	/**
	 * @param RenderingContextInterface $context
	 * @return void
	 */
	protected function storeBackup(RenderingContextInterface $context) {
		$this->backup = array(
			$context->getViewHelperVariableContainer()->get('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchCaseValue'),
			$this->determineBooleanOf($context, 'switchBreakRequested'),
			$this->determineBooleanOf($context, 'switchContinueUntilBreak')
		);
	}

	/**
	 * @param RenderingContextInterface $context
	 * @return void
	 */
	protected function restoreBackup(RenderingContextInterface $context) {
		$context->getViewHelperVariableContainer()->add('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchCaseValue', $this->backup[0]);
		$context->getViewHelperVariableContainer()->add('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchBreakRequested', $this->backup[1]);
		$context->getViewHelperVariableContainer()->add('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', 'switchContinueUntilBreak', $this->backup[2]);
	}

	/**
	 * @param RenderingContextInterface $context
	 * @param mixed $var
	 * @return boolean
	 */
	protected function determineBooleanOf($context, $var) {
		if (TRUE === $context->getViewHelperVariableContainer()->exists('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', $var)) {
			return $context->getViewHelperVariableContainer()->get('FluidTYPO3\Vhs\ViewHelpers\SwitchViewHelper', $var);
		}
		return FALSE;
	}

}
