<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\AssetService;
use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Base class for ViewHelpers capable of registering assets
 * which will be included when rendering the page.
 *
 * Note: building of all Assets takes place in the class
 * FluidTYPO3\Vhs\Service\AssetService with two reasons:
 *
 * - A "buildAll" method should never be possible to call
 *   from any Asset ViewHelper; it should only be possible
 *   from a single class.
 * - The method but must be public and non-static and thus
 *   cannot be hidden from access by subclasses if placed
 *   in this class.
 */
abstract class AbstractAssetViewHelper extends AbstractViewHelper implements AssetInterface
{

    use ArrayConsumingViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var AssetService
     */
    protected $assetService;

    /**
     * @var array
     */
    private static $settingsCache = null;

    /**
     * @var array
     */
    private $assetSettingsCache;

    /**
     * @var array
     */
    protected $localSettings;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder
     */
    protected $tagBuilder;

    /**
     * Example types: raw, meta, css, js.
     *
     * If a LESS stylesheet is being compiled the "type"
     * would be "css" because this will group the compiled
     * LESS stylesheet with other CSS - allowing it to be
     * merged with other CSS.
     *
     * @var string
     */
    protected $type = 'raw';


    /**
     * @param ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param AssetService $assetService
     * @return void
     */
    public function injectAssetService(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->tagBuilder = $this->objectManager->get(TagBuilder::class);
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'Content to insert in header/footer');
        $this->registerArgument('path', 'string', 'If not using tag content, specify path to file here');
        $this->registerArgument(
            'external',
            'boolean',
            'If TRUE and standalone, includes the file as raw URL. If TRUE and not standalone then downloads ' .
            'the file and merges it when building Assets',
            false,
            false
        );
        $this->registerArgument(
            'name',
            'string',
            'Optional name of the content. If multiple occurrences of the same name happens, behavior is defined ' .
            'by argument "overwrite"'
        );
        $this->registerArgument(
            'overwrite',
            'boolean',
            'If set to FALSE and a relocated string with "name" already exists, does not overwrite the existing ' .
            'relocated string. Default behavior is to overwrite.',
            false,
            true
        );
        $this->registerArgument(
            'dependencies',
            'string',
            'CSV list of other named assets upon which this asset depends. When included, this asset will always ' .
            'load after its dependencies'
        );
        $this->registerArgument(
            'group',
            'string',
            'Optional name of a logical group (created dynamically just by using the name) to which this particular ' .
            'asset belongs.',
            false,
            'fluid'
        );
        $this->registerArgument(
            'debug',
            'boolean',
            'If TRUE, outputs information about this ViewHelper when the tag is used. Two master debug switches ' .
            'exist in TypoScript; see documentation about Page / Asset ViewHelper'
        );
        $this->registerArgument(
            'standalone',
            'boolean',
            'If TRUE, excludes this Asset from any concatenation which may be applied'
        );
        $this->registerArgument(
            'rewrite',
            'boolean',
            'If FALSE, this Asset will be included as is without any processing of contained urls',
            false,
            true
        );
        $this->registerArgument(
            'fluid',
            'boolean',
            'If TRUE, renders this (standalone or external) Asset as if it were a Fluid template, passing along ' .
            'values of the "variables" attribute or every available template variable if "variables" not specified',
            false,
            false
        );
        $this->registerArgument(
            'variables',
            'mixed',
            'An optional array of arguments which you use inside the Asset, be it standalone or inline. Use this ' .
            'argument to ensure your Asset filenames are only reused when all variables used in the Asset are the same',
            false,
            false
        );
        $this->registerArgument(
            'movable',
            'boolean',
            'If TRUE, allows this Asset to be included in the document footer rather than the header. Should never ' .
            'be allowed for CSS.',
            false,
            true
        );
        $this->registerArgument(
            'trim',
            'boolean',
            'DEPRECATED. Trim is no longer supported. Setting this to TRUE doesn\'t do anything.',
            false,
            false
        );
        $this->registerArgument(
            'namedChunks',
            'boolean',
            'If FALSE, hides the comment containing the name of each of Assets which is merged in a merged file. ' .
            'Disable to avoid a bit more output at the cost of transparency',
            false,
            false
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * Render method
     *
     * @return void
     */
    public function render()
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling'])
            || !$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling']
        ) {
            $this->finalize();
        }
    }

    /**
     * Build this asset. Override this method in the specific
     * implementation of an Asset in order to:
     *
     * - if necessary compile the Asset (LESS, SASS, CoffeeScript etc)
     * - make a final rendering decision based on arguments
     *
     * Note that within this function the ViewHelper and TemplateVariable
     * Containers are not dependable, you cannot use the ControllerContext
     * and RenderingContext and you should therefore also never call
     * renderChildren from within this function. Anything else goes; CLI
     * commands to build, caching implementations - you name it.
     *
     * @return mixed
     */
    public function build()
    {
        if (false === isset($this->arguments['path']) || true === empty($this->arguments['path'])) {
            return $this->getContent();
        }
        if (true === isset($this->arguments['external']) && true === (boolean) $this->arguments['external']) {
            $path = $this->arguments['path'];
        } else {
            $path = GeneralUtility::getFileAbsFileName($this->arguments['path']);
        }
        $content = file_get_contents($path);
        return $content;
    }

    /**
     * Saves this Asset or perhaps discards it if overriding is
     * disabled and an identically named Asset already exists.
     *
     * Performed from every Asset's render() for it to work.
     *
     * @return void
     */
    protected function finalize()
    {
        $this->assetSettingsCache = null;
        $this->localSettings = null;
        if (!isset($GLOBALS['VhsAssets'])) {
            $GLOBALS['VhsAssets'] = [];
        }
        $name = $this->getName();
        $overwrite = $this->getOverwrite();
        $slotFree = !isset($GLOBALS['VhsAssets'][$name]);
        if (!($overwrite || $slotFree)) {
            return;
        }
        $this->content = $this->getContent();
        $this->tagBuilder->setContent($this->content);
        $this->debug();
        $GLOBALS['VhsAssets'][$name] = clone $this;
    }

    /**
     * @return mixed
     */
    protected function debug()
    {
        $settings = $this->getSettings();
        $debugOutputEnabled = $this->assertDebugEnabled();
        $useDebugUtility = !isset($settings['useDebugUtility'])
            || (isset($settings['useDebugUtility']) && $settings['useDebugUtility']);
        $debugInformation = $this->getDebugInformation();
        if ($debugOutputEnabled) {
            if ($useDebugUtility) {
                DebuggerUtility::var_dump($debugInformation);
                return '';
            } else {
                return var_export($debugInformation, true);
            }
        }
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        $assetSettings = $this->getAssetSettings();
        if (isset($assetSettings['dependencies'])) {
            return GeneralUtility::trimExplode(',', $assetSettings['dependencies'], true);
        }
        return [];
    }

    /**
     * @return boolean
     */
    protected function getOverwrite()
    {
        $assetSettings = $this->getAssetSettings();
        return (isset($assetSettings['overwrite']) && $assetSettings['overwrite']);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $assetSettings = $this->getAssetSettings();
        if (isset($assetSettings['name']) && !empty($assetSettings['name'])) {
            $name = $assetSettings['name'];
        } else {
            $name = md5(serialize($assetSettings));
        }
        return $name;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        $assetSettings = $this->getAssetSettings();
        return $assetSettings['group'];
    }

    /**
     * @return string
     */
    protected function getContent()
    {
        $assetSettings = $this->getAssetSettings();
        if (isset($assetSettings['content']) && !empty($assetSettings['content'])) {
            $content = $assetSettings['content'];
        } else {
            $content = $this->renderChildren();
        }
        return $content;
    }

    /**
     * @return string
     */
    protected function getTagWithContent()
    {
        return $this->tagBuilder->render();
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        $assetSettings = $this->getAssetSettings();
        if (isset($assetSettings['variables']) && is_array($assetSettings['variables'])) {
            return $assetSettings['variables'];
        }
        return [];
    }

    /**
     * Returns the settings used by this particular Asset
     * during inclusion. Public access allows later inspection
     * of the TypoScript values which were applied to the Asset.
     *
     * @return array
     */
    public function getSettings()
    {
        if (null === self::$settingsCache) {
            $allTypoScript = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            $settingsExist = isset($allTypoScript['plugin.']['tx_vhs.']['settings.']);
            if (!$settingsExist) {
                // no settings exist, but don't allow a NULL value. This prevents cache clobbering.
                self::$settingsCache = [];
            } else {
                self::$settingsCache = GeneralUtility::removeDotsFromTS(
                    $allTypoScript['plugin.']['tx_vhs.']['settings.']
                );
            }
        }
        $settings = self::$settingsCache;
        if (is_array($this->localSettings)) {
            ArrayUtility::mergeRecursiveWithOverrule($settings, $this->localSettings);
        }
        return $settings;
    }

    /**
     * @param array|\ArrayAccess $settings
     */
    public function setSettings($settings)
    {
        if (is_array($settings) || $settings instanceof \ArrayAccess) {
            $this->localSettings = $settings;
        }
    }

    /**
     * @return array
     */
    public function getAssetSettings()
    {
        if (is_array($this->assetSettingsCache)) {
            return $this->assetSettingsCache;
        }
        // Note: name and group are taken directly from arguments; if they are changed through
        // TypoScript the changed values will be returned from this function.
        $name = $this->arguments['name'];
        $groupName = $this->arguments['group'];
        $settings = $this->getSettings();
        $assetSettings = $this->arguments;
        $assetSettings['type'] = $this->getType();
        if (isset($settings['asset']) && true === is_array($settings['asset'])) {
            $assetSettings = $this->mergeArrays($assetSettings, $settings['asset']);
        }
        if (isset($settings['assetGroup'][$groupName]) && is_array($settings['assetGroup'][$groupName])) {
            $assetSettings = $this->mergeArrays($assetSettings, $settings['assetGroup'][$groupName]);
        }
        if (isset($settings['asset'][$name]) && is_array($settings['asset'][$name])) {
            $assetSettings = $this->mergeArrays($assetSettings, $settings['asset'][$name]);
        }
        if (!empty($assetSettings['path']) && !$assetSettings['external']) {
            $assetSettings['path'] = GeneralUtility::getFileAbsFileName($assetSettings['path']);
        }
        $assetSettings['name'] = $name;
        $this->assetSettingsCache = $assetSettings;
        return $assetSettings;
    }

    /**
     * Allows public access to debug this particular Asset
     * instance later, when including the Asset in the page.
     *
     * @return array
     */
    public function getDebugInformation()
    {
        return [
            'class' => get_class($this),
            'settings' => $this->getAssetSettings()
        ];
    }

    /**
     * Returns TRUE of settings specify that the source of this
     * Asset should be rendered as if it were a Fluid template,
     * using variables from the "arguments" attribute
     *
     * @return boolean
     */
    public function assertFluidEnabled()
    {
        $settings = $this->getAssetSettings();
        if (true === (isset($settings['fluid']) && $settings['fluid'] > 0)) {
            return true;
        }
        return false;
    }

    /**
     * Returns TRUE if settings specify that the name of each Asset
     * should be placed above the built content when placed in merged
     * Asset cache files.
     *
     * @return boolean
     */
    public function assertAddNameCommentWithChunk()
    {
        $settings = $this->getAssetSettings();
        return ((isset($settings['namedChunks']) && $settings['namedChunks']) || !isset($settings['namedChunks']));
    }

    /**
     * Returns TRUE if the current Asset should be debugged as commanded
     * by settings in TypoScript an/ord ViewHelper attributes.
     *
     * @return boolean
     */
    public function assertDebugEnabled()
    {
        $settings = $this->getSettings();
        if (isset($settings['debug']) && $settings['debug']) {
            return true;
        }
        $settings = $this->getAssetSettings();
        if (isset($settings['asset']['debug']) && $settings['asset']['debug'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function assertAllowedInFooter()
    {
        $settings = $this->getAssetSettings();
        return (isset($settings['movable']) && $settings['movable']);
    }

    /**
     * @return boolean
     */
    public function assertHasBeenRemoved()
    {
        $groupName = $this->arguments['group'];
        $settings = $this->getSettings();
        $dependencies = $this->getDependencies();
        array_push($dependencies, $this->getName());
        foreach ($dependencies as $name) {
            if (isset($settings['asset'][$name]['remove']) && $settings['asset'][$name]['remove'] > 0) {
                return true;
            }
        }
        if (isset($settings['assetGroup'][$groupName]['remove']) && $settings['assetGroup'][$groupName]['remove'] > 0) {
            return true;
        }
        return false;
    }
}
