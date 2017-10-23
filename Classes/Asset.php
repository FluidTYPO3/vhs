<?php
namespace FluidTYPO3\Vhs;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Asset\AssetInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * ### Asset
 *
 * Class to create Assets in PHP.
 *
 * ### Examples:
 *
 *     $asset = $this->objectManager->get('FluidTYPO3\\Vhs\\Asset');
 *     // OR you can use the static factory method which works anywhere
 *     // including outside of Extbase.
 *     $asset = \FluidTYPO3\Vhs\Asset::getInstance();
 *     $asset->setPath('fileadmin/test.js')->setStandalone(TRUE)->finalize();
 *
 * Or simply:
 *
 *     $this->objectManager->get('FluidTYPO3\\Vhs\\Asset')->setPath('...')->finalize();
 *
 * And you can create clean instances:
 *
 *
 * Depending on how you need to access the Asset afterwards, you will
 * want wo switch between these methods.
 *
 * Or if you have all settings in an array (with members named according to
 * the properties on this class:
 *
 *     \FluidTYPO3\Vhs\Asset::createFromSettings($settings)->finalize();
 *
 * Finally, if your Asset is file based, VHS can perform a few detections to
 * set initial attributes like standalone, external (if file contains protocol),
 * type (based on extension) and name (base name of file).
 *
 *     \FluidTYPO3\Vhs\Asset::createFromFile($filePathAndFilename);
 *     \FluidTYPO3\Vhs\Asset::createFromUrl($urlToExternalFile);
 *
 * You can chain all setters on the class before finally calling finalize() to
 * register the Asset (you can still modify the Asset afterwards, but an Asset
 * that has not been finalize()'ed will never show up or be processed - which
 * is a lot friendlier than requiring you to use remove() on unwanted Assets).
 *
 * > Note: the "createFrom..." suite of methods automatically calls finalize()
 * > on your Asset just before returning it. You can of course keep modifying
 * > the instance after it is returned - but when using a "createFrom"... method
 * > VHS assumes you always want your Asset included in the output.
 */
class Asset implements AssetInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $dependencies = [];

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $content = null;

    /**
     * @var string
     */
    protected $path = null;

    /**
     * @var boolean
     */
    protected $namedChunks = false;

    /**
     * @var boolean
     */
    protected $movable = true;

    /**
     * @var boolean
     */
    protected $removed = false;

    /**
     * @var boolean
     */
    protected $fluid = false;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var boolean
     */
    protected $external = false;

    /**
     * @var boolean
     */
    protected $standalone = false;

    /**
     * @var boolean
     */
    protected $rewrite = true;

    /**
     * @var array
     */
    private static $settingsCache = null;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @return Asset
     */
    public static function getInstance()
    {
        /** @var $asset Asset */
        $asset = GeneralUtility::makeInstance(ObjectManager::class)->get(Asset::class);
        return $asset;
    }

    /**
     * @param array $settings
     * @return Asset
     */
    public static function createFromSettings(array $settings)
    {
        $asset = self::getInstance();
        foreach ($settings as $propertyName => $value) {
            ObjectAccess::setProperty($asset, $propertyName, $value);
        }
        return $asset->finalize();
    }

    /**
     * @param string $filePathAndFilename
     * @return Asset
     */
    public static function createFromFile($filePathAndFilename)
    {
        $asset = self::getInstance();
        $asset->setExternal(false);
        $asset->setPath($filePathAndFilename);
        return $asset->finalize();
    }

    /**
     * @param string $content
     * @return Asset
     */
    public static function createFromContent($content)
    {
        $asset = self::getInstance();
        $asset->setContent($content);
        $asset->setName(md5($content));
        return $asset->finalize();
    }

    /**
     * @param string $url
     * @return Asset
     */
    public static function createFromUrl($url)
    {
        $asset = self::getInstance();
        $asset->setStandalone(true);
        $asset->setExternal(true);
        $asset->setPath($url);
        return $asset->finalize();
    }

    /**
     * Render method
     *
     * @return mixed
     */
    public function render()
    {
        return $this->build();
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
        $path = $this->getPath();
        if (true === empty($path)) {
            return $this->getContent();
        }
        $content = file_get_contents($path);
        return $content;
    }

    /**
     * @return Asset
     */
    public function finalize()
    {
        $name = $this->getName();
        if (true === empty($name)) {
            $name = md5($this->standalone . '//' . $this->type . '//' . $this->path . '//' . $this->content);
            if ($this->fluid) {
                $name .= '_' . md5(serialize($this->variables));
            }
        }
        if (false === isset($GLOBALS['VhsAssets']) || false === is_array($GLOBALS['VhsAssets'])) {
            $GLOBALS['VhsAssets'] = [];
        }
        $GLOBALS['VhsAssets'][$name] = $this;
        return $this;
    }

    /**
     * @return Asset
     */
    public function remove()
    {
        return $this->setRemoved(true);
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param array $dependencies
     * @return Asset
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Asset
     */
    public function setType($type)
    {
        $this->type = $type;
        if ('css' == strtolower($type)) {
            $this->setMovable(false);
        }
        return $this;
    }

    /**
     * @param boolean $external
     * @return Asset
     */
    public function setExternal($external)
    {
        $this->external = $external;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getExternal()
    {
        return $this->external;
    }

    /**
     * @param boolean $rewrite
     * @return Asset
     */
    public function setRewrite($rewrite)
    {
        $this->rewrite = $rewrite;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getRewrite()
    {
        return $this->rewrite;
    }

    /**
     * @param boolean $standalone
     * @return Asset
     */
    public function setStandalone($standalone)
    {
        $this->standalone = $standalone;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getStandalone()
    {
        return $this->standalone;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Asset
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $path = (0 === strpos($this->path, '/') ? $this->path : GeneralUtility::getFileAbsFileName($this->path));
        if (true === empty($this->content) && null !== $this->path && file_exists($path)) {
            return file_get_contents($path);
        }
        return $this->content;
    }

    /**
     * @param string $content
     * @return Asset
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Asset
     */
    public function setPath($path)
    {
        if (null === $path) {
            $this->path = null;
            return $this;
        }
        if (false === strpos($path, '://') && 0 !== strpos($path, '/')) {
            $path = GeneralUtility::getFileAbsFileName($path);
        }
        if (null === $this->type) {
            $this->setType(pathinfo($path, PATHINFO_EXTENSION));
        }
        if (null === $this->name) {
            $this->setName(pathinfo($path, PATHINFO_FILENAME));
        }
        $this->path = $path;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getNamedChunks()
    {
        return $this->namedChunks;
    }

    /**
     * @param boolean $namedChunks
     * @return Asset
     */
    public function setNamedChunks($namedChunks)
    {
        $this->namedChunks = $namedChunks;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getFluid()
    {
        return $this->fluid;
    }

    /**
     * @param boolean $fluid
     * @return Asset
     */
    public function setFluid($fluid)
    {
        $this->fluid = $fluid;
        return $this;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     * @return Asset
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
        return $this;
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
            if (true === $settingsExist) {
                self::$settingsCache = GeneralUtility::removeDotsFromTS(
                    $allTypoScript['plugin.']['tx_vhs.']['settings.']
                );
            }
        }
        $settings = (array) self::$settingsCache;
        $properties = get_class_vars(get_class($this));
        foreach (array_keys($properties) as $propertyName) {
            $properties[$propertyName] = $this->$propertyName;
        }

        ArrayUtility::mergeRecursiveWithOverrule($settings, $this->settings);
        ArrayUtility::mergeRecursiveWithOverrule($settings, $properties);
        return $settings;
    }

    /**
     * @param array $settings
     * @return Asset
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return array
     */
    public function getAssetSettings()
    {
        return $this->getSettings();
    }

    /**
     * @return boolean
     */
    public function getMovable()
    {
        return $this->movable;
    }

    /**
     * @param boolean $movable
     * @return $this
     */
    public function setMovable($movable)
    {
        $this->movable = $movable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getRemoved()
    {
        return $this->removed;
    }

    /**
     * @param boolean $removed
     * @return Asset
     */
    public function setRemoved($removed)
    {
        $this->removed = $removed;
        return $this;
    }

    /**
     * Allows public access to debug this particular Asset
     * instance later, when including the Asset in the page.
     *
     * @return array
     */
    public function getDebugInformation()
    {
        return $this->getSettings();
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
        return $this->getFluid();
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
        return $this->getNamedChunks();
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
        $enabled = (true === isset($settings['debug']) ? (boolean) $settings['debug'] : false);
        return $enabled;
    }

    /**
     * @return boolean
     */
    public function assertAllowedInFooter()
    {
        return $this->getMovable();
    }

    /**
     * @return boolean
     */
    public function assertHasBeenRemoved()
    {
        return $this->getRemoved();
    }
}
