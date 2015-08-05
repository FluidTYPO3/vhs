<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ### Render: Template
 *
 * Render a template file (with arguments if desired).
 *
 * Supports passing variables and controlling the format,
 * paths can be overridden and uses the same format as TS
 * settings a' la plugin.tx_myext.view, which means that
 * this can be done (from any extension, not just "foo")
 *
 *     <v:render.template
 * 	    file="EXT:foo/Resources/Private/Templates/Action/Show.html"
 *      variables="{object: customLoadedObject}"
 *      paths="{v:variable.typoscript(path: 'plugin.tx_foo.view')}"
 *      format="xml" />
 *
 * Which would render the "show" action's template from
 * EXT:foo using paths define in that extension's typoscript
 * but using a custom loaded object when rendering the template
 * rather than the object defined by the "Action" controller
 * of EXT:foo. The output would be in XML format and this
 * format would also be respected by Layouts and Partials
 * which are rendered from the Show.html template.
 *
 * As such this is very similar to Render/RequestViewHelper
 * with two major differences:
 *
 * 1. A true ControllerContext is not present when rendering which
 *    means that links generated in the template should be made
 *    always including all parameters from ExtensionName over
 *    PluginName through the usual action etc.
 * 2. The Controller from EXT:foo is not involved in any way,
 *    which means that any custom variables the particular
 *    template depends on must be added manually through
 *    the "variables" argument
 *
 * Consider using Render/InlineViewHelper if you are rendering
 * templates from the same plugin.
 *
 * Consider using Render/RequestViewHelper if you require a
 * completely isolated rendering identical to that which takes
 * place when rendering an Extbase plugin's content object.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
class TemplateViewHelper extends AbstractRenderViewHelper {

	/**
	 * Renders a template using custom variables, format and paths
	 *
	 * @param string $file Path to template file, EXT:myext/... paths supported
	 * @param array $variables Optional array of template variables when rendering
	 * @param string $format Optional format of the template(s) being rendered
	 * @param string $paths Optional array (plugin.tx_myext.view style) of paths, EXT:mypath/... paths supported
	 * @return string
	 */
	public function render($file = NULL, $variables = [], $format = NULL, $paths = NULL) {
		if (NULL === $file) {
			$file = $this->renderChildren();
		}
		$file = GeneralUtility::getFileAbsFileName($file);
		$view = $this->getPreparedView();
		$view->setTemplatePathAndFilename($file);
		$view->assignMultiple($variables);
		if (NULL !== $format) {
			$view->setFormat($format);
		}
		if (TRUE === is_array($paths)) {
			if (TRUE === isset($paths['layoutRootPath'])) {
				$paths['layoutRootPath'] = 0 === strpos($paths['layoutRootPath'], 'EXT:') ? GeneralUtility::getFileAbsFilename($paths['layoutRootPath']) : $paths['layoutRootPath'];
				$view->setLayoutRootPath($paths['layoutRootPath']);
			}
			if (TRUE === isset($paths['partialRootPath'])) {
				$paths['partialRootPath'] = 0 === strpos($paths['partialRootPath'], 'EXT:') ? GeneralUtility::getFileAbsFilename($paths['partialRootPath']) : $paths['partialRootPath'];
				$view->setPartialRootPath($paths['partialRootPath']);
			}
		}
		return $this->renderView($view);
	}

}
