# VHS Change log

2.3.2 - 2015-04-16
------------------

- [#798](https://github.com/FluidTYPO3/vhs/pull/798) v:page.header.meta got *'property'* attribute - used for open graph metadata
  - [v:page.header.meta](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Header/MetaViewHelper.html)

- [#799](https://github.com/FluidTYPO3/vhs/pull/799) v:iterator.filter got *'nullFilter'* attribute - allows to filter NULL or empty values
  - [v:iterator.filter](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/FilterViewHelper.html)

- v:or 'arguments' array also applies on 'alternative' text
  - Format is same, as in PHP [sprintf](http://php.net/manual/ru/function.sprintf.php) 
  - [v:or](https://fluidtypo3.org/viewhelpers/vhs/master/OrViewHelper.html)

2.3.1 - 2015-03-15
------------------

- No important changes

2.3.0 - 2015-03-09
------------------

- :exclamation: Legacy namespace support completely removed
  - It is no longer possible to use any of VHS classes by their legacy names. Switch to the proper vendor and namespace.

- Reusable Traits implemented to extend ViewHelper capabilities, as a replacement for ViewHelperUtility:
  - [BasicViewHelperTrait](https://github.com/FluidTYPO3/vhs/commit/0630c1a685b36b3bf799220a8e06b9c57ccadefe)
  - [ArrayConsumingViewHelperTrait](https://github.com/FluidTYPO3/vhs/commit/0630c1a685b36b3bf799220a8e06b9c57ccadefe)
  - [TemplateVariableViewHelperTrait](https://github.com/FluidTYPO3/vhs/commit/0630c1a685b36b3bf799220a8e06b9c57ccadefe)
  - [TagViewHelperTrait](https://github.com/FluidTYPO3/vhs/commit/7def7c1cb1f0cb5d125465cdd65a854851b6d7e3)

- :exclamation: Support of TYPO3 6.0 and 6.1 was dropped

- :exclamation: Minimum PHP requirement is now 5.4.0 due to the use of Traits
  - [Details](https://github.com/FluidTYPO3/vhs/commit/d1b732dbcd61fbdfd27df323265cbcb77618b4a3)
  - [Reason for raising requirement](https://github.com/FluidTYPO3/vhs/commit/0630c1a685b36b3bf799220a8e06b9c57ccadefe)

- [#734](https://github.com/FluidTYPO3/vhs/pull/734) :exclamation: **v:format.url.sanitizeString** changed
  - Became deprecated - use **v:format.sanitizeString** instead
  - Got *'customMap'* attribute - allows to override built-in replacement mapping with custom one
  - [v:format.sanitizeString](https://fluidtypo3.org/viewhelpers/vhs/master/Format/SanitizeStringViewHelper.html)

- :exclamation: CompilableAsset concept removed
  - [Details](https://github.com/FluidTYPO3/vhs/commit/c56b224d83886539112e0ee5e270218ad0bee8ad)

- [#735](https://github.com/FluidTYPO3/vhs/pull/735) Context ViewHelpers added:  **v:condition.context.isDevelopment**,  **v:condition.context.isProduction**, **v:condition.context.isTesting** and **v:context.get**
  - [v:condition.context.isDevelopment](https://fluidtypo3.org/viewhelpers/vhs/master/Condition/Context/IsDevelopmentViewHelper.html) - returns TRUE if application context is 'Development' or a sub-context of it
  - [v:condition.context.isProduction](https://fluidtypo3.org/viewhelpers/vhs/master/Condition/Context/IsProductionViewHelper.html) - returns TRUE if application context is 'Production' or a sub-context of it
  - [v:condition.context.isTesting](https://fluidtypo3.org/viewhelpers/vhs/master/Condition/Context/IsTestingViewHelper.html) - returns TRUE if application context is 'Testing' or a sub-context of it
  - [v:context.get](https://fluidtypo3.org/viewhelpers/vhs/master/Context/GetViewHelper.html) - returns the current application context which may include possible sub-contexts
  - **Note**: these ViewHelpers will work on TYPO3 >= 6.2 only. [Read more about contexts in TYPO3](http://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Bootstrapping/Index.html#bootstrapping-context)

- [#771](https://github.com/FluidTYPO3/vhs/pull/771) **v:variable.extensionConfiguration** added - reads extensions settings located in ext_conf_template.txt
  - [v:variable.extensionConfiguration](https://fluidtypo3.org/viewhelpers/vhs/master/Variable/ExtensionConfigurationViewHelper.html) 

- [#746](https://github.com/FluidTYPO3/vhs/pull/746) **v:resource.language** added - reads a language file and returns all the translations from it
  - [v:resource.language](https://fluidtypo3.org/viewhelpers/vhs/master/Resource/LanguageViewHelper.html)

- [#770](https://github.com/FluidTYPO3/vhs/pull/770) [#773](https://github.com/FluidTYPO3/vhs/pull/773) v:media.youtube got more control attributres:
  - *'hideControl'* - hide player's control bar
  - *'hideInfo'* - hide player's info bar
  - *'playlist'* - comma seperated list of video IDs to be played
  - *'loop'* - play the video in a loop
  - *'start'* - start playing after seconds
  - *'end'* - stop playing after seconds
  - *'lightTheme'* - use the player's light theme
  - *'videoQuality'* - set the player's video quality (hd1080,hd720,highres,large,medium,small)
  - *'windowMode'* - Set the Window-Mode of the player (transparent,opaque). This is necessary for z-index handling in IE10/11.
  - [v:media.youtube](https://fluidtypo3.org/viewhelpers/vhs/master/Media/YoutubeViewHelper.html)

- [#751](https://github.com/FluidTYPO3/vhs/pull/751) v:iterator.filter also accepts an array as 'filter' attribute; in case of array provided as filter, each value of 'subject' is compared with each value of 'filter'

- [#757](https://github.com/FluidTYPO3/vhs/pull/757) v:iterator.merge can be used in a chain
  - suggested usage: `{abc -> v:iterator.merge(b: def)}`
  - [v:iterator.merge](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/MergeViewHelper.html)

- Contents of Fluid assets (asset's attribute *fluid="TRUE"*) can be stored (or overriden) with TS 'content' property
  - See [commit message](https://github.com/FluidTYPO3/vhs/commit/a14e2bbe1734e20509399513d987626f3a28bda3)
  - Check [asset TS settings](https://github.com/FluidTYPO3/vhs#asset-settings)

- [#740](https://github.com/FluidTYPO3/vhs/pull/740) v:page.languageMenu got *'excludeQueryVars'* attribute - set to comma-separate list of GET variables to exclude from generated link
  - [v:page.languageMenu](https://fluidtypo3.org/viewhelpers/vhs/master/Page/LanguageMenuViewHelper.html)

- v:debug got more intelligence in debugging ObjectAccessors - shows properties only accessible in Fluid
  - [v:debug](https://fluidtypo3.org/viewhelpers/vhs/master/DebugViewHelper.html)

2.2.0 - 2014-12-03
------------------

- Added support of TYPO3 7.x

2.1.4 - 2014-11-16
------------------

- [#710](https://github.com/FluidTYPO3/vhs/pull/710) :exclamation: Asset's *'arguments'* property is removed - use *'variables'* instead
  - [v:asset.prefetch](https://fluidtypo3.org/viewhelpers/vhs/master/Asset/PrefetchViewHelper.html)
  - [v:asset.script](https://fluidtypo3.org/viewhelpers/vhs/master/Asset/ScriptViewHelper.html)
  - [v:asset.style](https://fluidtypo3.org/viewhelpers/vhs/master/Asset/StyleViewHelper.html)

- :exclamation: MenuViewHelpers attribute *'useShortcutUid'* default value was changed from TRUE to FALSE
  - [v:page.menu.browse](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/BrowseViewHelper.html)
  - [v:page.menu.deferred](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/DeferredViewHelper.html)
  - [v:page.menu.directory](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/DirectoryViewHelper.html)
  - [v:page.menu.list](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/ListViewHelper.html)
  - [v:page.breadCrumb](https://fluidtypo3.org/viewhelpers/vhs/master/Page/BreadCrumbViewHelper.html)
  - [v:page.menu](https://fluidtypo3.org/viewhelpers/vhs/master/Page/MenuViewHelper.html)

2.1.3 - 2014-10-24
------------------

- [#688](https://github.com/FluidTYPO3/vhs/pull/688) **v:condition.type.isBoolean** added
  - [v:condition.type.isBoolean](https://fluidtypo3.org/viewhelpers/vhs/master/Condition/Type/IsBooleanViewHelper.html)

- [#697](https://github.com/FluidTYPO3/vhs/pull/697) **v:format.removeXss** added - accepts *'string'* as argument and cleans it out from possible XSS
  - Not included in VH reference, so please check [commit](https://github.com/FluidTYPO3/vhs/commit/9bcb298d10722110401dca48263159f3ae5405a0)

2.1.2 - 2014-10-04
------------------

- [#684](https://github.com/FluidTYPO3/vhs/pull/684) v:page.languageMenu got *'configuration'* attribute - holds additional typoLink configuration
  - [v:page.languageMenu](https://fluidtypo3.org/viewhelpers/vhs/master/Page/LanguageMenuViewHelper.html)

2.1.1 - 2014-10-03
------------------

- No important changes

2.1.0 - 2014-10-03
------------------

- [#681](https://github.com/FluidTYPO3/vhs/pull/681) **v:tag** added - generate dynamic HTML tag names without breaking XHTML validation and with nice features, like disabling empty attributes
  - Check [release notes](https://fluidtypo3.org/blog/news/vhs-21-released.html) to find out more about this VH
  - [v:tag](https://fluidtypo3.org/viewhelpers/vhs/master/TagViewHelper.html)

- v:iterator.filter got *'invert'* attribute - inverts the behaviour of the ViewHelper, so the filtered element is removed from the array
  - [v:iterator.filter](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/FilterViewHelper.html)

2.0.2 - 2014-09-19
------------------

- :exclamation: Asset's *'arguments'* property is deprecated - use *'variables'* instead
  - [v:asset.prefetch](https://fluidtypo3.org/viewhelpers/vhs/master/Asset/PrefetchViewHelper.html)
  - [v:asset.script](https://fluidtypo3.org/viewhelpers/vhs/master/Asset/ScriptViewHelper.html)
  - [v:asset.style](https://fluidtypo3.org/viewhelpers/vhs/master/Asset/StyleViewHelper.html)

- v:iterator.extract got *'single'* attribute - returns the first value instead of always returning all values
  - [v:iterator.extract](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/ExtractViewHelper.html)

2.0.1 - 2014-09-05
------------------

- No important changes

2.0.0 - 2014-09-05
------------------

- [#545](https://github.com/FluidTYPO3/vhs/pull/545) :exclamation: PHP namespaces support
  - VHS now uses the FluidTYPO3\Vhs namespace which means you are advised to change all your Fluid namespace inclusions.
  - `{namespace v=Tx_Vhs_ViewHelpers}` -> `{namespace v=FluidTYPO3\Vhs\ViewHelpers}`
  - `xmlns:v="http://fedext.net/ns/vhs/ViewHelpers"` -> `xmlns:v="http://typo3.org/ns/FluidTYPO3/Vhs/ViewHelpers"`
  - [More info](https://fluidtypo3.org/blog/news/vhs-20-released.html)

- [#540](https://github.com/FluidTYPO3/vhs/pull/540) :exclamation: Deprecated ViewHelpers removed
  - **v:var** namespace is now **v:variable**
  - **v:if** namespace is now **v:condition**
  - **v:condition** ViewHelper is removed (use **v:if**)
  - all the ViewHelpers from **v:condition** namespace were reworked - check VHS [reference page](https://fluidtypo3.org/viewhelpers/vhs/master.html)
  - **v:form.hasValidator** is now **v:condition.form.hasValidator**
  - **v:form.required** is now **v:condition.form.isRequired**
  - all the client-information ViewHelpers: **..client.isBrowser**, **..client.isMobile**, **..client.isSystem** removed
  - **v:if.condition** - use **v:if**, it can do the same
  - **v:if.condition.extend** - use **v:if**, it can do the same
  - **v:iterator.contains** is now **v:condition.iterator.contains**
  - **v:page.content.footer** is now **v:page.footer**
  - **v:page.content.get** is now **v:content.get**
  - **v:page.content.render** is now **v:content.render**
  - **v:page.siteUrl** is now **v:site.url**
  - **v:var.isset** is now **v:condition.variable.isset**

- [#643](https://github.com/FluidTYPO3/vhs/pull/643) :exclamation: ImageInfoViewHelpers attribute *path* renamed to *src*
  - [v:media.image.height](https://fluidtypo3.org/viewhelpers/vhs/master/Media/Image/HeightViewHelper.html)
  - [v:media.image.width](https://fluidtypo3.org/viewhelpers/vhs/master/Media/Image/WidthViewHelper.html)
  - [v:media.image.mimetype](https://fluidtypo3.org/viewhelpers/vhs/master/Media/Image/MimetypeViewHelper.html)

- :exclamation: *'allowMoveToFooter'* property renamed to *'movable'* in asset definitions through TypoScript
  - Check [commit message](https://github.com/FluidTYPO3/vhs/commit/0e2c6ea90a2efc9abd09622d4299ba4184b7da47) for details

- :exclamation: *'showHidden'* property is deprecated in MenuViewHelpers
  - Check [commit message](https://github.com/FluidTYPO3/vhs/commit/ce1513ef21d0c23bd4db26e8ae4c1048b8dd0906) for a reason
  - [v:page.menu.browse](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/BrowseViewHelper.html)
  - [v:page.menu.deferred](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/DeferredViewHelper.html)
  - [v:page.menu.directory](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/DirectoryViewHelper.html)
  - [v:page.menu.list](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/ListViewHelper.html)
  - [v:page.breadCrumb](https://fluidtypo3.org/viewhelpers/vhs/master/Page/BreadCrumbViewHelper.html)
  - [v:page.menu](https://fluidtypo3.org/viewhelpers/vhs/master/Page/MenuViewHelper.html)

- Workspaces support in menus added
  - [Commit message](https://github.com/FluidTYPO3/vhs/commit/1146b2dadf00efc8a757c8b01d6b8f6defdb5f42)

- [#534](https://github.com/FluidTYPO3/vhs/pull/534) **v:content.info** added - ViewHelper to access data of the current content element record
  - [v:content.info](https://fluidtypo3.org/viewhelpers/vhs/master/Content/InfoViewHelper.html)

- **v:variable.register.get** and **v:variable.register.set** added - allow to work with TSFE registers
  - [v:variable.register.get](https://fluidtypo3.org/viewhelpers/vhs/master/Variable/Register/GetViewHelper.html)
  - [v:variable.register.set](https://fluidtypo3.org/viewhelpers/vhs/master/Variable/Register/SetViewHelper.html)

- **v:media.audio** added - renders HTML code to embed a HTML5 audio player
  - [v:media.audio](https://fluidtypo3.org/viewhelpers/vhs/master/Media/AudioViewHelper.html)

- **v:iterator.keys** added - gets keys from an iterator
  - [v:iterator.keys](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/KeysViewHelper.html)

- [#578](https://github.com/FluidTYPO3/vhs/pull/578) **v:resource.collection** added - returns a TYPO3 collection (records or files)
  - [v:resource.collection](https://fluidtypo3.org/viewhelpers/vhs/master/Resource/CollectionViewHelper.html)
  
- [#538](https://github.com/FluidTYPO3/vhs/pull/538) ImageViewHelpers got *'format'* and *'quality'* attributes
  - [v:media.image](https://fluidtypo3.org/viewhelpers/vhs/master/Media/ImageViewHelper.html)
  - [v:uri.image](https://fluidtypo3.org/viewhelpers/vhs/master/Uri/ImageViewHelper.html)

- [#629](https://github.com/FluidTYPO3/vhs/pull/629) ImageViewHelpers got *'maxW'*, *'maxH'*, *'minW'*, *'minH'* attributes
  - [v:media.image](https://fluidtypo3.org/viewhelpers/vhs/master/Media/ImageViewHelper.html)
  - [v:uri.image](https://fluidtypo3.org/viewhelpers/vhs/master/Uri/ImageViewHelper.html)

- [#626](https://github.com/FluidTYPO3/vhs/pull/626) v:iterator.sort can now combine sort flags in *'sortFlags'*
  - [v:iterator.sort](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/SortViewHelper.html)

- [#634](https://github.com/FluidTYPO3/vhs/pull/634) ImageInfoViewHelpers support FAL now via new attributes: *'treatIdAsUid'* and *'treatIdAsReference'*
  - [v:media.image.height](https://fluidtypo3.org/viewhelpers/vhs/master/Media/Image/HeightViewHelper.html)
  - [v:media.image.width](https://fluidtypo3.org/viewhelpers/vhs/master/Media/Image/WidthViewHelper.html)
  - [v:media.image.mimetype](https://fluidtypo3.org/viewhelpers/vhs/master/Media/Image/MimetypeViewHelper.html)

- [#635](https://github.com/FluidTYPO3/vhs/pull/635) v:iterator.chunk fills up missing chunks/elements with NULLs, when *'fixed'* is TRUE
  - [v:iterator.chunk](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/ChunkViewHelper.html)

- [#641](https://github.com/FluidTYPO3/vhs/pull/641) v:iterator.chunk got *'preserveKeys'* attribute
  - [v:iterator.chunk](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/ChunkViewHelper.html)

- v:page.menu.browse got *'pageUid'* attribute
  - [v:page.menu.browse](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/BrowseViewHelper.html)

- [#660](https://github.com/FluidTYPO3/vhs/pull/660) v:page.menu.browse got *'currentPageUid'* attribute
  - [v:page.menu.browse](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Menu/BrowseViewHelper.html)

- [#620](https://github.com/FluidTYPO3/vhs/pull/620) v:page.languageMenu got *'pageUid'* attribute
  - [v:page.languageMenu](https://fluidtypo3.org/viewhelpers/vhs/master/Page/LanguageMenuViewHelper.html)

- ContentViewHelpers got *'sectionIndexOnly'* attribute - allows to include content elements which are indicated as "Include in section index" in content attributes
  - [v:render.record](https://fluidtypo3.org/viewhelpers/vhs/master/Render/RecordViewHelper.html)
  - [v:content.get](https://fluidtypo3.org/viewhelpers/vhs/master/Content/GetViewHelper.html)
  - [v:content.render](https://fluidtypo3.org/viewhelpers/vhs/master/Content/RenderViewHelper.html)
  - [v:content.random.get](https://fluidtypo3.org/viewhelpers/vhs/master/Content/Random/GetViewHelper.html)
  - [v:content.random.render](https://fluidtypo3.org/viewhelpers/vhs/master/Content/Random/RenderViewHelper.html)

- [#552](https://github.com/FluidTYPO3/vhs/pull/552) Mount points are supported in menus

- `vhs_main` and `vhs_markdown` caching configurations added, so you may configure to use your own caching backend (e.g. Redis) instead of DB
