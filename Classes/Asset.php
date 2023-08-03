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
 * the properties on this class):
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
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    protected array $dependencies = [];
    protected string $type = '';
    protected string $name = '';
    protected ?string $content = null;
    protected ?string $path = null;
    protected bool $namedChunks = false;
    protected bool $movable = true;
    protected bool $removed = false;
    protected bool $fluid = false;
    protected array $variables = [];
    protected array $settings = [];
    protected bool $external = false;
    protected bool $standalone = false;
    protected bool $async = false;
    protected bool $defer = false;
    protected bool $rewrite = true;
    private static ?array $settingsCache = null;

    public function __construct()
    {
        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $this->configurationManager = $configurationManager;
    }

    public static function getInstance(): self
    {
        /** @var Asset $asset */
        $asset = GeneralUtility::makeInstance(Asset::class);
        return $asset;
    }

    public static function createFromSettings(array $settings): self
    {
        $asset = static::getInstance();
        foreach ($settings as $propertyName => $value) {
            ObjectAccess::setProperty($asset, $propertyName, $value);
        }
        return $asset->finalize();
    }

    public static function createFromFile(string $filePathAndFilename): self
    {
        $asset = static::getInstance();
        $asset->setExternal(false);
        $asset->setName((string) pathinfo($filePathAndFilename, PATHINFO_FILENAME));
        $asset->setPath($filePathAndFilename);
        return $asset->finalize();
    }

    public static function createFromContent(string $content): self
    {
        $asset = static::getInstance();
        $asset->setContent($content);
        $asset->setName(md5($content));
        return $asset->finalize();
    }

    public static function createFromUrl(string $url): self
    {
        $asset = static::getInstance();
        $asset->setStandalone(true);
        $asset->setExternal(true);
        $asset->setPath($url);
        return $asset->finalize();
    }

    public function render(): ?string
    {
        return $this->build();
    }

    /**
     * Build this asset. Override this method in the specific
     * implementation of an Asset in order to:
     *
     * - if necessary compile the Asset (LESS, SASS, CoffeeScript etc.)
     * - make a final rendering decision based on arguments
     *
     * Note that within this function the ViewHelper and TemplateVariable
     * Containers are not dependable, you cannot use the ControllerContext
     * and RenderingContext and you should therefore also never call
     * renderChildren from within this function. Anything else goes; CLI
     * commands to build, caching implementations - you name it.
     */
    public function build(): ?string
    {
        $path = $this->getPath();
        if (empty($path)) {
            return $this->getContent();
        }
        $content = file_get_contents($path);
        return $content ?: null;
    }

    public function finalize(): self
    {
        $name = $this->getName();
        if (empty($name)) {
            $name = md5($this->standalone . '//' . $this->type . '//' . $this->path . '//' . $this->content);
            if ($this->fluid) {
                $name .= '_' . md5(serialize($this->variables));
            }
        }
        if (!isset($GLOBALS['VhsAssets']) || !is_array($GLOBALS['VhsAssets'])) {
            $GLOBALS['VhsAssets'] = [];
        }
        $GLOBALS['VhsAssets'][$name] = $this;
        return $this;
    }

    public function remove(): self
    {
        return $this->setRemoved(true);
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function setDependencies(array $dependencies): self
    {
        $this->dependencies = $dependencies;
        return $this;
    }

    public function getType(): string
    {
        if (empty($this->type) && !empty($this->path)) {
            return pathinfo($this->path, PATHINFO_EXTENSION);
        }
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        if ('css' == strtolower($type)) {
            $this->setMovable(false);
        }
        return $this;
    }

    public function setExternal(bool $external): self
    {
        $this->external = $external;
        return $this;
    }

    public function getExternal(): bool
    {
        return $this->external;
    }

    public function setRewrite(bool $rewrite): self
    {
        $this->rewrite = $rewrite;
        return $this;
    }

    public function getRewrite(): bool
    {
        return $this->rewrite;
    }

    public function setStandalone(bool $standalone): self
    {
        $this->standalone = $standalone;
        return $this;
    }

    public function getStandalone(): bool
    {
        return $this->standalone;
    }

    public function setAsync(bool $async): self
    {
        $this->async = $async;
        return $this;
    }

    public function getAsync(): bool
    {
        return $this->async;
    }

    public function setDefer(bool $defer): self
    {
        $this->defer = $defer;
        return $this;
    }

    public function getDefer(): bool
    {
        return $this->defer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getContent(): ?string
    {
        if ($this->path === null) {
            return $this->content;
        }
        $path = (0 === strpos($this->path, '/') ? $this->path : GeneralUtility::getFileAbsFileName($this->path));
        if (empty($this->content) && !empty($this->path) && file_exists($path)) {
            return (string) file_get_contents($path);
        }
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        if (null === $path) {
            $this->path = null;
            return $this;
        }
        if (false === strpos($path, '://') && 0 !== strpos($path, '/')) {
            $path = GeneralUtility::getFileAbsFileName($path);
        }
        if ($this->type) {
            $this->setType(pathinfo($path, PATHINFO_EXTENSION));
        }
        if (empty($this->name)) {
            $this->setName(pathinfo($path, PATHINFO_FILENAME));
        }
        $this->path = $path;
        return $this;
    }

    public function getNamedChunks(): bool
    {
        return $this->namedChunks;
    }

    public function setNamedChunks(bool $namedChunks): self
    {
        $this->namedChunks = $namedChunks;
        return $this;
    }

    public function getFluid(): bool
    {
        return $this->fluid;
    }

    public function setFluid(bool $fluid): self
    {
        $this->fluid = $fluid;
        return $this;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * Returns the settings used by this particular Asset
     * during inclusion. Public access allows later inspection
     * of the TypoScript values which were applied to the Asset.
     */
    public function getSettings(): array
    {
        if (null === self::$settingsCache) {
            $allTypoScript = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            self::$settingsCache = GeneralUtility::removeDotsFromTS(
                $allTypoScript['plugin.']['tx_vhs.']['settings.'] ?? []
            );
        }
        $settings = (array) self::$settingsCache;
        $properties = get_class_vars(get_class($this));
        $skipProperties = ['settingsCache', 'configurationManager'];
        foreach (array_keys($properties) as $propertyName) {
            if (in_array($propertyName, $skipProperties, true)) {
                unset($properties[$propertyName]);
                continue;
            }
            $properties[$propertyName] = $this->$propertyName;
        }

        if (empty($properties['type']) && !empty($properties['path'])) {
            $properties['type'] = pathinfo($properties['path'], PATHINFO_EXTENSION);
        }

        ArrayUtility::mergeRecursiveWithOverrule($settings, $this->settings);
        ArrayUtility::mergeRecursiveWithOverrule($settings, $properties);
        return $settings;
    }

    public function setSettings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    public function getAssetSettings(): array
    {
        return $this->getSettings();
    }

    public function getMovable(): bool
    {
        return $this->movable;
    }

    public function setMovable(bool $movable): self
    {
        $this->movable = $movable;
        return $this;
    }

    public function getRemoved(): bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): self
    {
        $this->removed = $removed;
        return $this;
    }

    /**
     * Allows public access to debug this particular Asset
     * instance later, when including the Asset in the page.
     */
    public function getDebugInformation(): array
    {
        return $this->getSettings();
    }

    /**
     * Returns TRUE of settings specify that the source of this
     * Asset should be rendered as if it were a Fluid template,
     * using variables from the "arguments" attribute
     */
    public function assertFluidEnabled(): bool
    {
        return $this->getFluid();
    }

    /**
     * Returns TRUE if settings specify that the name of each Asset
     * should be placed above the built content when placed in merged
     * Asset cache files.
     */
    public function assertAddNameCommentWithChunk(): bool
    {
        return $this->getNamedChunks();
    }

    /**
     * Returns TRUE if the current Asset should be debugged as commanded
     * by settings in TypoScript an/ord ViewHelper attributes.
     */
    public function assertDebugEnabled(): bool
    {
        $settings = $this->getSettings();
        $enabled = (bool) ($settings['debug'] ?? false);
        return $enabled;
    }

    public function assertAllowedInFooter(): bool
    {
        return $this->getMovable();
    }

    public function assertHasBeenRemoved(): bool
    {
        return $this->getRemoved();
    }
}
