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
 *      file="EXT:foo/Resources/Private/Templates/Action/Show.html"
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
 */
class TemplateViewHelper extends AbstractRenderViewHelper
{

    public function initializeArguments()
    {
        $this->registerArgument('file', 'string', 'Path to template file, EXT:myext/... paths supported', false);
        $this->registerArgument('variables', 'array', 'Optional array of template variables for rendering', false);
        $this->registerArgument('format', 'string', 'Optional format of the template(s) being rendered', false);
        $this->registerArgument(
            'paths',
            'array',
            'Optional array of arrays of layout and partial root paths, EXT:mypath/... paths supported',
            false
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        $file = $this->arguments['file'];
        if (null === $file) {
            $file = $this->renderChildren();
        }
        $file = GeneralUtility::getFileAbsFileName($file);
        $view = static::getPreparedView();
        $view->setTemplatePathAndFilename($file);
        if (is_array($this->arguments['variables'])) {
            $view->assignMultiple($this->arguments['variables']);
        }
        $format = $this->arguments['format'];
        if (null !== $format) {
            $view->setFormat($format);
        }
        $paths = $this->arguments['paths'];
        if (is_array($paths)) {
            if (isset($paths['layoutRootPaths']) && is_array($paths['layoutRootPaths'])) {
                $layoutRootPaths = $this->processPathsArray($paths['layoutRootPaths']);
                $view->setLayoutRootPaths($layoutRootPaths);
            }
            if (isset($paths['partialRootPaths']) && is_array($paths['partialRootPaths'])) {
                $partialRootPaths = $this->processPathsArray($paths['partialRootPaths']);
                $view->setPartialRootPaths($partialRootPaths);
            }
        }
        return static::renderView($view, $this->arguments);
    }

    /**
     * @param array $paths
     * @return array
     */
    protected function processPathsArray(array $paths)
    {
        $pathsArray = [];
        foreach ($paths as $key => $path) {
            $pathsArray[$key] = (0 === strpos($path, 'EXT:')) ? GeneralUtility::getFileAbsFileName($path) : $path;
        }

        return $pathsArray;
    }
}
