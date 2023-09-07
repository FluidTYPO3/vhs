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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

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
     * @var TagBuilder
     */
    protected $tagBuilder;

    protected static ?array $settingsCache = null;
    private ?array $assetSettingsCache = null;
    protected ?array $localSettings = null;
    protected ?string $content = null;

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
    protected string $type = 'raw';

    public function __construct()
    {
        /** @var TagBuilder $tagBuilder */
        $tagBuilder = GeneralUtility::makeInstance(TagBuilder::class);
        $this->tagBuilder = $tagBuilder;
    }

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function injectAssetService(AssetService $assetService): void
    {
        $this->assetService = $assetService;
    }

    public function initializeArguments(): void
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
        return (string) $this->build();
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
     */
    public function build(): ?string
    {
        /** @var string|null $path */
        $path = $this->arguments['path'];
        if (empty($path ?? false)) {
            return $this->content;
        }
        if (!($this->arguments['external'] ?? false)) {
            $path = GeneralUtility::getFileAbsFileName($path);
        }
        $content = file_get_contents($path);
        return $content ?: null;
    }

    /**
     * Saves this Asset or perhaps discards it if overriding is
     * disabled and an identically named Asset already exists.
     *
     * Performed from every Asset's render() for it to work.
     */
    protected function finalize(): void
    {
        $this->assetSettingsCache = null;
        $this->localSettings = null;
        if (!isset($GLOBALS['VhsAssets'])) {
            $GLOBALS['VhsAssets'] = [];
        }
        $name = $this->getName();
        $overwrite = $this->getOverwrite();
        if (!$overwrite && $this->assetService->isAlreadyDefined($name)) {
            return;
        }
        $this->content = (string) $this->getContent();
        $this->tagBuilder->setContent($this->content);
        $this->debug();
        $GLOBALS['VhsAssets'][$name] = clone $this;
    }

    protected function debug(): ?string
    {
        $settings = $this->getSettings();
        $debugOutputEnabled = $this->assertDebugEnabled();
        $useDebugUtility = (bool) ($settings['useDebugUtility'] ?? true);
        $debugInformation = $this->getDebugInformation();
        if ($debugOutputEnabled) {
            if ($useDebugUtility) {
                DebuggerUtility::var_dump($debugInformation);
                return '';
            } else {
                return var_export($debugInformation, true);
            }
        }
        return null;
    }

    public function getDependencies(): array
    {
        $assetSettings = $this->getAssetSettings();
        if (isset($assetSettings['dependencies'])) {
            return GeneralUtility::trimExplode(',', $assetSettings['dependencies'], true);
        }
        return [];
    }

    protected function getOverwrite(): bool
    {
        $assetSettings = $this->getAssetSettings();
        return (isset($assetSettings['overwrite']) && $assetSettings['overwrite']);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        $assetSettings = $this->getAssetSettings();
        if (isset($assetSettings['name']) && !empty($assetSettings['name'])) {
            $name = $assetSettings['name'];
        } else {
            $name = md5(serialize($assetSettings));
        }
        return $name;
    }

    public function getGroup(): string
    {
        $assetSettings = $this->getAssetSettings();
        return $assetSettings['group'];
    }

    protected function getContent(): ?string
    {
        $assetSettings = $this->getAssetSettings();
        if (isset($assetSettings['content']) && !empty($assetSettings['content'])) {
            $content = $assetSettings['content'];
        } else {
            $content = $this->renderChildren();
        }
        return $content;
    }

    protected function getTagWithContent(): string
    {
        return $this->tagBuilder->render();
    }

    public function getVariables(): array
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
     */
    public function getSettings(): array
    {
        if (null === static::$settingsCache) {
            $allTypoScript = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            $settingsExist = isset($allTypoScript['plugin.']['tx_vhs.']['settings.']);
            if (!$settingsExist) {
                // no settings exist, but don't allow a NULL value. This prevents cache clobbering.
                static::$settingsCache = [];
            } else {
                static::$settingsCache = GeneralUtility::removeDotsFromTS(
                    $allTypoScript['plugin.']['tx_vhs.']['settings.']
                );
            }
        }
        $settings = static::$settingsCache;
        if (is_array($this->localSettings)) {
            ArrayUtility::mergeRecursiveWithOverrule($settings, $this->localSettings);
        }
        return $settings;
    }

    public function setSettings(array $settings): void
    {
        $this->localSettings = $settings;
    }

    public function getAssetSettings(): array
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
        if (isset($settings['asset']) && is_array($settings['asset'])) {
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
     */
    public function getDebugInformation(): array
    {
        return [
            'class' => get_class($this),
            'settings' => $this->getAssetSettings()
        ];
    }

    /**
     * Returns TRUE of settings specify that the source of this
     * Asset should be rendered as if it were a Fluid template,
     * using variables from the "arguments" attribute.
     */
    public function assertFluidEnabled(): bool
    {
        $settings = $this->getAssetSettings();
        return ($settings['fluid'] ?? 0) > 0;
    }

    /**
     * Returns TRUE if settings specify that the name of each Asset
     * should be placed above the built content when placed in merged
     * Asset cache files.
     */
    public function assertAddNameCommentWithChunk(): bool
    {
        $settings = $this->getAssetSettings();
        return ((isset($settings['namedChunks']) && $settings['namedChunks']) || !isset($settings['namedChunks']));
    }

    /**
     * Returns TRUE if the current Asset should be debugged as commanded
     * by settings in TypoScript an/ord ViewHelper attributes.
     */
    public function assertDebugEnabled(): bool
    {
        $settings = $this->getSettings();
        if ($settings['debug'] ?? false) {
            return true;
        }
        $settings = $this->getAssetSettings();
        return $settings['asset']['debug'] ?? false;
    }

    public function assertAllowedInFooter(): bool
    {
        $settings = $this->getAssetSettings();
        return $settings['movable'] ?? false;
    }

    public function assertHasBeenRemoved(): bool
    {
        $groupName = $this->arguments['group'];
        $settings = $this->getSettings();
        $dependencies = $this->getDependencies();
        $dependencies[] = $this->getName();
        foreach ($dependencies as $name) {
            if ($settings['asset'][$name]['remove'] ?? false) {
                return true;
            }
        }
        return $settings['assetGroup'][$groupName]['remove'] ?? false;
    }
}
