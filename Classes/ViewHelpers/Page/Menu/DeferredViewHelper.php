<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ### Page: Deferred menu rendering ViewHelper
 *
 * Place this ViewHelper inside any other ViewHelper which
 * has been configured with the `deferred` attribute set to
 * TRUE - this will cause the output of the parent to only
 * contain the content of this ViewHelper.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class DeferredViewHelper extends AbstractMenuViewHelper {

	/**
	 * Initialize
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->overrideArgument('as', 'string', 'If used, stores the menu pages as an array in a variable named according to this value and renders the tag content - which means automatic rendering is disabled if this attribute is used', FALSE, NULL);
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function render() {
		$as = $this->arguments['as'];
		if (FALSE === $this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'deferredArray')) {
			return NULL;
		}
		if (FALSE === $this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'deferredString')) {
			return NULL;
		}
		if (NULL === $as) {
			$content = $this->viewHelperVariableContainer->get('FluidTYPO3\\Vhs\\ViewHelpers\\Page\\Menu\\AbstractMenuViewHelper', 'deferredString');
			$this->unsetDeferredVariableStorage();
			return $content;
		} elseif (TRUE === empty($as)) {
			throw new \Exception('An "as" attribute was used but was empty - use a proper string value', 1370096373);
		}
		if (TRUE === $this->templateVariableContainer->exists($as)) {
			$backupVariable = $this->templateVariableContainer->get($as);
			$this->templateVariableContainer->remove($as);
		}
		$this->templateVariableContainer->add($as, $this->viewHelperVariableContainer->get('FluidTYPO3\\Vhs\\ViewHelpers\\Page\\Menu\\AbstractMenuViewHelper', 'deferredArray'));
		$this->unsetDeferredVariableStorage();
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		if (TRUE === isset($backupVariable)) {
			$this->templateVariableContainer->add($as, $backupVariable);
		}
		return $content;
	}

}
