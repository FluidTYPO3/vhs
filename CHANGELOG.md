# VHS Change log

3.0.1 - 2016-07-29
------------------

- [#1067](https://github.com/FluidTYPO3/vhs/pull/1067) Argument `subject` on `v:format.preg.replace` made optional to allow usage with tag content / inline
- [#1068](https://github.com/FluidTYPO3/vhs/pull/1068) Page-related ViewHelpers operable in backend context
- [#1073](https://github.com/FluidTYPO3/vhs/pull/1073) Bug fix to show sub menus with single item on root level pages
- [#1074](https://github.com/FluidTYPO3/vhs/pull/1074) Bug fix to remove unintentionally shown pages from menus
- [#1077](https://github.com/FluidTYPO3/vhs/pull/1077) Added `JSON_HEX_APOS` to default JSON encoding parameters in `v:format.json.encode`


3.0.0 - 2016-07-12
------------------

    Cosmetic change: unmodified PSR-2 CGL and shorthand array syntax adopted.
    Contributions must now respect this CGL (`phpcs` as well as `phpcbf` are provided
    in the `vendor/bin` directory once installed and can be ran using `--standard=PSR2`).

- :exclamation: NEW DEPRECATIONS AND REMOVED DEPRECATED CODE
  - [49e2295](https://github.com/FluidTYPO3/vhs/commit/49e22956fccd789fb244a7d244443de22915040a) Deprecated VHS typoLink ViewHelpers (use core versions instead)
  - *'includeHidden'*, *'showHiddenInMenu'* attributes deprecated on all Menu ViewHelpers
  - *'linkAccessRestrictedPages'* was deprectaed in favor of *'showAccessProtected'* on all Menu ViewHelpers
  - *'resolveExclude'*, *'showHidden'*, *'excludeSubpageTypes'* attributes removed from all Menu ViewHelpers
  - Removed `FluidTYPO3\Vhs\Service\PageSelectService` (use `FluidTYPO3\Vhs\Service\PageService` and TYPO3 native `TYPO3\CMS\Frontend\Page\PageRepository` instead)
  - [#1023](https://github.com/FluidTYPO3/vhs/pull/1023) RegEx related ViewHelpers changed
  - *v:condition.string.preg* removed and **v:variable.pregMatch** should be used instead
  - *v:format.regularExpression* removed and **v:format.pregReplace** should be used instead
  - [v:variable.pregMatch](https://fluidtypo3.org/viewhelpers/vhs/master/Variable/PregMatchViewHelper.html)
  - [v:format.pregReplace](https://fluidtypo3.org/viewhelpers/vhs/master/Format/PregReplaceViewHelper.html)
  - [#1003](https://github.com/FluidTYPO3/vhs/pull/1003) v:switch and v:case ViewHelpers removed (use core versions instead)
  - [7ca6865](https://github.com/FluidTYPO3/vhs/commit/7ca686598c6d54ac6f3358198b09d71bd8032e2b) Argument `arguments` on Asset ViewHelpers removed (use `variables` instead)

- [#970](https://github.com/FluidTYPO3/vhs/pull/970) **PHP7** is supported

- [TYPO3 8 supported](https://github.com/FluidTYPO3/vhs/commit/5625467c951b2ee762d88da21169ade08605e7d0)

- :exclamation: [#974](https://github.com/FluidTYPO3/vhs/pull/974) *'allowMoveToFooter'* attribute removed from asset ViewHelpers
	- use *'movable'* instead

- :exclamation: [fc1baf9](https://github.com/FluidTYPO3/vhs/commit/fc1baf90b07f6036ccec74e53730c8c2f66e6af6) *'name'* attribute removed from v:variable.extensionConfiguration
	- use *'path'* instead
	- [v:variable.extensionConfiguration](https://fluidtypo3.org/viewhelpers/vhs/master/Variable/ExtensionConfigurationViewHelper.html)

- :exclamation: [#1003](https://github.com/FluidTYPO3/vhs/pull/1003) v:switch and v:case ViewHelpers removed
	- use **f:switch** and **f:case** from Fluid itself
	- check the notes from PR [#1003](https://github.com/FluidTYPO3/vhs/pull/1003) for migration instruction

- :exclamation: [#987](https://github.com/FluidTYPO3/vhs/pull/987) Major refactoring of menu ViewHelpers
	- **WIP**: the list of changes and work on this isn't complete yet. Feel free to add more items here to provide a better overview of the work, which was done.
	- All the menu ViewHelpers are relocated from *v:page.menu.[vh-name]* to **v:menu.[vh-name]**
	- Removed `FluidTYPO3\Vhs\Service\PageSelectService` (use `FluidTYPO3\Vhs\Service\PageService` and TYPO3 native `TYPO3\CMS\Frontend\Page\PageRepository` instead)
	- **v:condition.page.hasSubpages**
		- *'includeHiddenInMenu'* attribute added - include pages hidden in menu
		- *'includeAccessProtected'* attribute added - includes pages, protected by FE groups constraints, see [#976](https://github.com/FluidTYPO3/vhs/issues/976)  
		- *'includeHidden'*, *'showHiddenInMenu'* attributes deprecated
	- All the menu ViewHelpers
		- *'showAccessProtected'* attribute added - if `TRUE` links to access protected pages are always rendered regardless of user login status
		- *'classAccessProtected'* attribute added - optional class name to add to links which are access protected
		- *'classAccessGranted'* attribute added - optional class name to add to links which are access protected but access is actually granted (user is logged in with corrrect FE usergroup)
		- *'resolveExclude'*, *'showHidden'*, *'excludeSubpageTypes'* attributes removed
	- All the menu objects, used in Fluid manual menu rendering
		- got *'accessProtected'* property, which indicates, that page is protected by FE groups constraints
		- got *'accessGranted'* property, which indicates, that page is protected by FE groups, but currently logged in user can access the page
		- *'hasSubPages'* property was renamed to **'hasSubpages'** (lowercase P)
	- [Source commit with more info](https://github.com/FluidTYPO3/vhs/commit/ee6956109be02d1c8fd75db6e4c4732e417bf184)
	- [Followup no. 1](https://github.com/FluidTYPO3/vhs/commit/0400fb8297099db5a863cedbb3aaa1f8f9d64361)
	- [Followup no. 2](https://github.com/FluidTYPO3/vhs/commit/5b47ccca9484b399b5e7b3710f964e6b4077a8aa)
    - [Followup no. 3](https://github.com/FluidTYPO3/vhs/commit/342953013812788a29c58d33031d72221c737c6e)


- :exclamation: [#1024](https://github.com/FluidTYPO3/vhs/pull/1024) v:page.link functionality was aligned with same link creation behavior, which was introdiced in menu ViewHelpers, regarding shrotcuts and protected pages
	- Received new attributes *'showAccessProtected'*, *'classAccessProtected'*, *'classAccessGranted'*, *'useShortcutUid'*, *'useShortcutTarget'*, *'useShortcutData'*
	- *'linkAccessRestrictedPages'* was deprectaed in favor of *'showAccessProtected'*
	- [v:page.link](https://fluidtypo3.org/viewhelpers/vhs/master/Page/LinkViewHelper.html)

- [#1022](https://github.com/FluidTYPO3/vhs/pull/1022) All the possible ViewHelpers are static compilable, which results in major performance improve
	- Discover [Static compilable](http://blog.reelworx.at/detail/fluid-compilable-speed-it-up/)

- [#955](https://github.com/FluidTYPO3/vhs/pull/955) **v:unless** ViewHelper added
	- Inverted *f:if* which only supports negative matching
	- [v:unless](https://fluidtypo3.org/viewhelpers/vhs/master/UnlessViewHelper.html)

- [#988](https://github.com/FluidTYPO3/vhs/pull/988) **v:iterator.diff** ViewHelper added
	- Computes the difference of arrays/Traversables
	- [v:iterator.diff](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/DiffViewHelper.html)

- [#1018](https://github.com/FluidTYPO3/vhs/pull/1018) **v:iterator.unique** ViewHelper added
	- Accepts a subject and returns or assigns a new uniques-filtered array
	- [v:iterator.unique](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/UniqueViewHelper.html)

- [#1019](https://github.com/FluidTYPO3/vhs/pull/1019) **v:iterator.column** ViewHelper added
	- Allows doing everything that array_column in PHP does but also supporting Iterator as input
	- [v:iterator.column](https://fluidtypo3.org/viewhelpers/vhs/master/Iterator/ColumnViewHelper.html)

- [#958](https://github.com/FluidTYPO3/vhs/pull/958) v:page.header.alternate got *'addQueryString'* attribute
	- If *TRUE*, the current query parameters will be kept in the URI
	- [v:page.header.alternate](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Header/AlternateViewHelper.html)

- [#980](https://github.com/FluidTYPO3/vhs/pull/980) v:media.youtube got *'enableJsApi'* attribute
	- Adds `enablejsapi=1` to a list of YouTube parameters
	- [v:media.youtube](https://fluidtypo3.org/viewhelpers/vhs/master/Media/YoutubeViewHelper.html)

- [aff7034](https://github.com/FluidTYPO3/vhs/commit/aff7034dc135a704b9d946d3f87df6bdc0a3790f) v:variable.extensionConfiguration returns a complete extension settings in case NULL provided as 'path'
	- [v:variable.extensionConfiguration](https://fluidtypo3.org/viewhelpers/vhs/master/Variable/ExtensionConfigurationViewHelper.html)

- [6851420](https://github.com/FluidTYPO3/vhs/commit/6851420557ef00f5efcfb1ab8ad871c0b7c990a0) Image and ImageInfo ViewHelpers cna now correctly recognize a FileReference, supplied as 'src', so no need to set up manually 'treatIdAsUid' and 'treatIdAsReference' attributes

- [#966](https://github.com/FluidTYPO3/vhs/pull/966) Fixed links of type "external" in menu ViewHelpers
	- [Source commit with more info](https://github.com/FluidTYPO3/vhs/commit/39a7c1820887199a1aade1c7bdd4b9dfd0ecd5eb)

- [#981](https://github.com/FluidTYPO3/vhs/pull/981) Fixed `player_id` argument for Vimeo player

- [#982](https://github.com/FluidTYPO3/vhs/pull/982) Fixed rendering of restricted pages by menu ViewHelpers

- [#984](https://github.com/FluidTYPO3/vhs/pull/984) Fixed *'caseSensitive'* behavior of v:format.replace

- [#994](https://github.com/FluidTYPO3/vhs/pull/994) Fixed deprecation warnings with `TSFE->getPageRenderer()`

- [#995](https://github.com/FluidTYPO3/vhs/pull/995) Fixed *'addQueryString'* behavior in v:page.languageMenu

- [#997](https://github.com/FluidTYPO3/vhs/pull/997) Fixed path to SVG flag icons in v:page.languageMenu

- Easter egg: over [1000](https://github.com/FluidTYPO3/vhs/pull/1000) commitments into repository!

- [#1001](https://github.com/FluidTYPO3/vhs/pull/1001) v:format.placeholder.image now loads the placeholder via http**s**: https://placehold.it

- [#953](https://github.com/FluidTYPO3/vhs/pull/953) v:page.resources.fal respects enableFileds, when sliding records by rootline

- [#1015](https://github.com/FluidTYPO3/vhs/pull/1015) v:render.template is aware of TYPO3 7 paths definition (layoutRootPath**s** and partialRootPath**s**)

- [#1042](https://github.com/FluidTYPO3/vhs/pull/1042) v:page.header.canonical includes query string, like with `addQueryString`

- [#1044](https://github.com/FluidTYPO3/vhs/pull/1044) Fix duplicate content in workspace previews

- [8085f56](https://github.com/FluidTYPO3/vhs/commit/8085f5621112f1d23aaabd8c89d464dc2f7b71b8) v:media.source respect fully qualified uris

- [456d709](https://github.com/FluidTYPO3/vhs/commit/456d709413351ceb2bc9a478a7e54bb032f9e5dc) src-set of image ViewHelper works correctly with `treatIdAsReference`

- [4ea3cb2](https://github.com/FluidTYPO3/vhs/commit/4ea3cb289ffc99e10b321bbe45b94610576d631f) Bug fix for incorrect type cast causing translated FAL records to be incorrectly detected

- [f38acef](https://github.com/FluidTYPO3/vhs/commit/f38acef2565d516b24c6cf89e7a67fec12165b3c) Bug fix for returned variable in `v:resource.collection`

- [62428e8](https://github.com/FluidTYPO3/vhs/commit/62428e82e76e01191ecbc4c9355bcfbe34cbfaab) Security condition ViewHelpers fixed to work in compiled templates

- [5e17260](https://github.com/FluidTYPO3/vhs/commit/5e17260ae34800fda6a183ff2b7f3353cfe70261) Bug fix for interpretation of `movable` argument on Assets

- [9c739c7](https://github.com/FluidTYPO3/vhs/commit/9c739c7d8179be6ad28fa8e6aa596f0e8f14163b) Bug fix to localise all FAL records individually

- [efa01ee](https://github.com/FluidTYPO3/vhs/commit/efa01eefe7a64867515682625df7faf439b356c6) Bug fix to exclude DELETED placeholders for FAL records when in workspace mode

- [2614c18[(https://github.com/FluidTYPO3/vhs/commit/2614c189ba08cf1b58c9f9fbbd250b7114e593f9) Bug fix to make image sub-ViewHelpers function with changed parent class

- [#1065](https://github.com/FluidTYPO3/vhs/pull/1065) Updated deprecated usage of PageRenderer

2.4.0 - 2015-11-19
------------------

- [TYPO3 7 LTS supported](https://github.com/FluidTYPO3/vhs/commit/ab893323e2cac57ca32b22efe20a7ad67c3e7bff)

- :exclamation: No more testing for PHP 5.4

- :exclamation: Support of TYPO3 6.2 dropped
	- For TYPO3 6.2 based projects there will be a *legacy* branch

- :exclamation: [#829](https://github.com/FluidTYPO3/vhs/pull/829) *'allowMoveToFooter'* attribute became deprecated in asset ViewHelpers
	- use *'movable'* instead

- :exclamation: [#856](https://github.com/FluidTYPO3/vhs/pull/856) *'pageUid'* attribute removed from v:render.request
	- In fact it was never used, so shouldn't break your code, unless you explicitly defined it
	- [v:render.request](https://fluidtypo3.org/viewhelpers/vhs/master/Render/RequestViewHelper.html)

- [#906](https://github.com/FluidTYPO3/vhs/pull/906) [#907](https://github.com/FluidTYPO3/vhs/pull/907) [#908](https://github.com/FluidTYPO3/vhs/pull/908) [#909](https://github.com/FluidTYPO3/vhs/pull/909) [#910](https://github.com/FluidTYPO3/vhs/pull/910) [#911](https://github.com/FluidTYPO3/vhs/pull/911) [#912](https://github.com/FluidTYPO3/vhs/pull/912) [#913](https://github.com/FluidTYPO3/vhs/pull/913) All the condition (v:condition.*) ViewHelpers are static compilable
	- Makes these ViewHelpers compatible with TYPO3 7.3+
	- Improves Fluid rendering speed 
	- Discover [Static compilable](http://blog.reelworx.at/detail/fluid-compilable-speed-it-up/)

- [#875](https://github.com/FluidTYPO3/vhs/pull/875) **Responsive images** support added
	- [v:media.image](https://fluidtypo3.org/viewhelpers/vhs/master/Media/ImageViewHelper.html) got *'srcset'* attribute, which contains CSV or Traversable with image widths
	- [v:media.image](https://fluidtypo3.org/viewhelpers/vhs/master/Media/ImageViewHelper.html) got *'srcsetDefault'* attribute, expected to contain default width, which should be used as fallback for non-srcset aware browsers
	- [v:media.picture](https://fluidtypo3.org/viewhelpers/vhs/master/Media/PictureViewHelper.html) - fully-functional implementation of [picturefill](http://scottjehl.github.io/picturefill/)

- [#825](https://github.com/FluidTYPO3/vhs/pull/825) v:page.resources.fal got *'slide'*, *'slideCollect'* and *'slideCollectReverse'* attributes, which enables page media to slide
	- [v:page.resources.fal](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Resources/FalViewHelper.html)
	- As a bonus, [SlideViewHelperTrait](https://github.com/FluidTYPO3/vhs/blob/4b2215d74b339f3014e1e0f866439cbb05cb2ff6/Classes/Traits/SlideViewHelperTrait.php) introduced

- [#884](https://github.com/FluidTYPO3/vhs/pull/884) **v:media.gravatar** and **v:uri.gravatar** ViewHelpers added
	- [v:media.gravatar](https://fluidtypo3.org/viewhelpers/vhs/master/Media/GravatarViewHelper.html)
	- [v:uri.gravatar](https://fluidtypo3.org/viewhelpers/vhs/master/Uri/GravatarViewHelper.html)
	- [Gravatar](https://en.gravatar.com/)

- [#945](https://github.com/FluidTYPO3/vhs/pull/945) v:variable.extensionConfiguration can fetch a subpart of extension configuration
	- *'path'* attribute introduced, which is responsible for this; contains TS-dotted path, like 'bar.baz'
	- :exclamation: *'name'* attribute became optional and deprecated
	- *'name'* and *'path'* are both optional, but at least one of them must be set
	- [v:variable.extensionConfiguration](https://fluidtypo3.org/viewhelpers/vhs/master/Variable/ExtensionConfigurationViewHelper.html)

- [#883](https://github.com/FluidTYPO3/vhs/pull/883) v:resource.image returns an img-tag instead of an array, when no 'as' attribute provided
	- [v:resource.image](https://fluidtypo3.org/viewhelpers/vhs/master/Resource/ImageViewHelper.html)

- [#861](https://github.com/FluidTYPO3/vhs/pull/861) All the menu ViewHelpers got *'forceAbsoluteUrl'* attribute
	- Forces menu items to contain absolute URLs
	- Default: `FALSE`

- v:page.resources.fal respects overrides in page localizations
	- [v:page.resources.fal](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Resources/FalViewHelper.html)
	- [Source commit with more info](https://github.com/FluidTYPO3/vhs/commit/59c9b3b3c9cc94e3e750d91f5f81dc3a6c9e675a)

- [#921](https://github.com/FluidTYPO3/vhs/pull/921) v:page.header.canonical respects RealURL configuration
	- :exclamation: Due to internal changes, attribute *'normalWhenNoLanguage'* is not used anymore and deprecated
	- [v:page.header.canonical](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Header/CanonicalViewHelper.html)

- v:format.tidy got *'encoding'* attribute 
	- Sets string encoding for Tidy
	- Default: `utf8`
	- [v:format.tidy](https://fluidtypo3.org/viewhelpers/vhs/master/Format/TidyViewHelper.html)
	- [Source commit with more info](https://github.com/FluidTYPO3/vhs/commit/f572be6a6c6ca261e1eee9bc74dc54e0dae0bb01)

- v:format.replace got *'caseSensitve'* attribute
	- Makes replacements case sensitive
	- Default: `TRUE`
	- [v:format.replace](https://fluidtypo3.org/viewhelpers/vhs/master/Format/ReplaceViewHelper.html)

- [#847](https://github.com/FluidTYPO3/vhs/pull/847) All the menu ViewHelpers got rid of hardcoded backup values
	- This allows to use any variable names in 'as' and 'rootLineAs' attributes

- [#879](https://github.com/FluidTYPO3/vhs/pull/879) All the menu ViewHelpers respect empty values in page translations

- [#763](https://github.com/FluidTYPO3/vhs/pull/763) All the menu ViewHelpers respect moved pages in workspaces

- [#854](https://github.com/FluidTYPO3/vhs/pull/854) v:page.languageMenu correctly detects TYPO3 version and provides appropriate path to flag-images 
	- [v:page.languageMenu](https://fluidtypo3.org/viewhelpers/vhs/master/Page/LanguageMenuViewHelper.html)

- [#871](https://github.com/FluidTYPO3/vhs/pull/871) v:page.languageMenu applies both: 'inactive' and 'current' classes, when this is a case
	- [v:page.languageMenu](https://fluidtypo3.org/viewhelpers/vhs/master/Page/LanguageMenuViewHelper.html)

- [#858](https://github.com/FluidTYPO3/vhs/pull/858) Force closing tag (instead of self-closing), when no file specified (or found) for asset

- [#922](https://github.com/FluidTYPO3/vhs/pull/922) v:page.resources.fal loads resources of *current* page by default
	- [v:page.resources.fal](https://fluidtypo3.org/viewhelpers/vhs/master/Page/Resources/FalViewHelper.html)

- [#865](https://github.com/FluidTYPO3/vhs/pull/865) v:resource.image respects fully qualified URIs
	- This VH can be used with external storages from now
	- [v:resource.image](https://fluidtypo3.org/viewhelpers/vhs/master/Resource/ImageViewHelper.html)

- [#905](https://github.com/FluidTYPO3/vhs/pull/905) v:page.menu correctly calculates number of translated sub-pages

- [#915](https://github.com/FluidTYPO3/vhs/pull/915) v:media.image correctly define paths to images, when `config.absRefPrefix` contains sub-folder in path
	- [v:media.image](https://fluidtypo3.org/viewhelpers/vhs/master/Media/ImageViewHelper.html)

- [#939](https://github.com/FluidTYPO3/vhs/pull/939) v:resource.record.fal handles workspaces better
	- [v:resource.record.fal](https://fluidtypo3.org/viewhelpers/vhs/master/Resource/Record/FalViewHelper.html)

- [#898](https://github.com/FluidTYPO3/vhs/pull/898) All the menu ViewHelpers are aware of possible *anonymous* users, when checking access rights against pages, links to which they generate
	- *anonymous* users are those, who doesn't have a concrete *user* object, but have *group* only, like is done in [EXT:sfpipauth](http://typo3.org/extensions/repository/view/sfpipauth)

- [#925](https://github.com/FluidTYPO3/vhs/pull/925) v:media.audio correctly handles audio-files with non-lowercase file extension
	- [v:media.audio](https://fluidtypo3.org/viewhelpers/vhs/master/Media/AudioViewHelper.html)

- [#934](https://github.com/FluidTYPO3/vhs/pull/934) `PageSelectService` is instantiated via `GeneralUtility::makeInstance()` instead of `new()`, making it possible to Xclass

- Bugfix to respect 'preload' argument of v:media.audio
	- [v:media.audio](https://fluidtypo3.org/viewhelpers/vhs/master/Media/AudioViewHelper.html)

2.3.3 - 2015-05-20
------------------

- [#826](https://github.com/FluidTYPO3/vhs/pull/826) **v:format.wordWrap** added - breaks a *'subject'* into strings with maximum size of *'limit'* (default = 80) characters, breaking with *'break'* (default = PHP_EOL) and concatenating them with *'glue'* (default = PHP_EOL)
  - [v:format.wordWrap](https://fluidtypo3.org/viewhelpers/vhs/master/Format/WordWrapViewHelper.html)

- [#819](https://github.com/FluidTYPO3/vhs/pull/819) v:format.eliminate got *'whitespaceBetweenHtmlTags'* attribute, which makes VH to remove all spaces between HTML tags
  - [v:format.eliminate](https://fluidtypo3.org/viewhelpers/vhs/master/Format/EliminateViewHelper.html)

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

- [#770](https://github.com/FluidTYPO3/vhs/pull/770) [#773](https://github.com/FluidTYPO3/vhs/pull/773) v:media.youtube got more control attributes:
  - *'hideControl'* - hide player's control bar
  - *'hideInfo'* - hide player's info bar
  - *'playlist'* - comma separated list of video IDs to be played
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

- Contents of Fluid assets (asset's attribute *fluid="TRUE"*) can be stored (or overridden) with TS 'content' property
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

- v:iterator.filter got *'invert'* attribute - inverts the behavior of the ViewHelper, so the filtered element is removed from the array
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
