<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### L (localisation) ViewHelper
 *
 * An extremely shortened and much more dev-friendly
 * alternative to f:translate. Automatically outputs
 * the name of the LLL reference if it is not found
 * and the default value is not set, making it much
 * easier to identify missing labels when translating.
 *
 * ### Examples
 *
 *     <v:l>some.label</v:l>
 *     <v:l key="some.label" />
 *     <v:l arguments="{0: 'foo', 1: 'bar'}">some.label</v:l>
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers
 */
class LViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('key', 'string', 'Translation Key');
		$this->registerArgument('id', 'string', 'Translation Key compatible to TYPO3 Flow');
		$this->registerArgument('default', 'string', 'if the given locallang key could not be found, this value is used. If this argument is not set, child nodes will be used to render the default');
		$this->registerArgument('htmlEscape', 'boolean', 'TRUE if the result should be htmlescaped. This won\'t have an effect for the default value');
		$this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string');
		$this->registerArgument('extensionName', 'string', 'UpperCamelCased extension key (for example BlogExample)');
	}

	/**
	 * Render method
	 * @return string
	 */
	public function render() {
		if (TRUE === isset($this->arguments['id']) && FALSE === empty($this->arguments['id'])) {
			$id = $this->arguments['id'];
		} else {
			$id = $this->arguments['key'];
		}
		$default = $this->arguments['default'];
		$htmlEscape = (boolean) $this->arguments['htmlEscape'];
		$arguments = $this->arguments['arguments'];
		$extensionName = $this->arguments['extensionName'];
		if (TRUE === empty($id)) {
			$id = $this->renderChildren();
		}
		if (TRUE === empty($default)) {
			$default = $id;
		}
		if (TRUE === empty($extensionName)) {
			if (TRUE === method_exists($this, 'getControllerContext')) {
				$request = $this->getControllerContext()->getRequest();
			} else {
    			$request = $this->controllerContext->getRequest();
			}
			$extensionName = $request->getControllerExtensionName();
		}
		$value = LocalizationUtility::translate($id, $extensionName, $arguments);
		if (TRUE === empty($value)) {
			$value = $default;
			if (TRUE === is_array($arguments)) {
				$value = vsprintf($value, $arguments);
			}
		} elseif (TRUE === $htmlEscape) {
			$value = htmlspecialchars($value);
		}
		return $value;
	}

}
