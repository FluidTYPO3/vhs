<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ### Variable: TypoScript
 *
 * Accesses Typoscript paths. Contrary to the Fluid-native
 * `f:cObject` this ViewHelper does not render objects but
 * rather retrieves the values. For example, if you retrieve
 * a TypoScript path to a TMENU object you will receive the
 * array of TypoScript defining the menu - not the rendered
 * menu HTML.
 *
 * A great example of how to use this ViewHelper is to very
 * quickly migrate a TypoScript-menu-based site (for example
 * currently running TemplaVoila + TMENU-objects) to a Fluid
 * ViewHelper menu based on `v:page.menu` or `v:page.breadCrumb`
 * by accessing key configuration options such as `entryLevel`
 * and even various `wrap` definitions.
 *
 * A quick example of how to parse a `wrap` TypoScript setting
 * into two variables usable for a menu item:
 *
 *     <!-- This piece to be added as far up as possible in order to prevent multiple executions -->
 *     <v:variable.set name="menuSettings" value="{v:variable.typoscript(path: 'lib.menu.main.stdWrap')}" />
 *     <v:variable.set name="wrap" value="{menuSettings.wrap -> v:iterator.explode(glue: '|')}" />
 *
 *     <!-- This in the loop which renders the menu (see "VHS: manual menu rendering" in FAQ): -->
 *     {wrap.0}{menuItem.title}{wrap.1}
 *
 *     <!-- An additional example to demonstrate very compact conditions which prevent wraps from being displayed -->
 *     {wrap.0 -> f:if(condition: settings.wrapBefore)}{menuItem.title}{wrap.1 -> f:if(condition: settings.wrapAfter)}
 */
class TyposcriptViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var ConfigurationManagerInterface
     */
    protected static $configurationManager;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('path', 'string', 'Path to TypoScript value or configuration array');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $path = $renderChildrenClosure();
        if (true === empty($path)) {
            return null;
        }
        $all = static::getConfigurationManager()->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $segments = explode('.', $path);
        $value = $all;
        foreach ($segments as $path) {
            $value = (true === isset($value[$path . '.']) ? $value[$path . '.'] : $value[$path]);
        }
        if (true === is_array($value)) {
            $value = GeneralUtility::removeDotsFromTS($value);
        }
        return $value;
    }

    /**
     * Returns instance of the configuration manager
     *
     * @return \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected static function getConfigurationManager()
    {
        if (null !== static::$configurationManager) {
            return static::$configurationManager;
        }
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        static::$configurationManager = $configurationManager;
        return $configurationManager;
    }
}
