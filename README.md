[![Latest Stable Version](https://poser.pugx.org/fluidtypo3/vhs/v/stable.svg?style=flat-square)](https://extensions.typo3.org/extension/vhs/)
[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-orange.svg?style=flat-square)](https://get.typo3.org/version/10)
[![TYPO3 9](https://img.shields.io/badge/TYPO3-9-orange.svg?style=flat-square)](https://get.typo3.org/version/9)
[![TYPO3 8](https://img.shields.io/badge/TYPO3-8-orange.svg?style=flat-square)](https://get.typo3.org/version/8)
[![Total Downloads](https://poser.pugx.org/fluidtypo3/vhs/d/total?style=flat-square)](https://packagist.org/packages/fluidtypo3/vhs)
[![Monthly Downloads](https://poser.pugx.org/fluidtypo3/vhs/d/monthly?style=flat-square)](https://packagist.org/packages/fluidtypo3/vhs)
[![Build Status](https://img.shields.io/travis/FluidTYPO3/vhs.svg?style=flat-square&label=package)](https://travis-ci.org/FluidTYPO3/vhs)
[![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/vhs/development.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/vhs)

<img src="https://fluidtypo3.org/logo.svgz" width="100%" />

# TYPO3 extension `vhs`

This is a collection of ViewHelpers for performing rendering tasks that are not
natively provided by TYPO3's [Fluid templating engine](https://docs.typo3.org/other/typo3/view-helper-reference/10.4/en-us/).
These include advanced formatters, mathematical calculators, special conditions,
and iterators and array calculators and processors.

|                  | URL                                                 |
|------------------|-----------------------------------------------------|
| **Repository:**  | https://github.com/FluidTYPO3/vhs                   |
| **Read online:** | https://viewhelpers.fluidtypo3.org/fluidtypo3/vhs/  |
| **TER:**         | https://extensions.typo3.org/extension/vhs          |

## Installation

The latest version can be installed via composer by running

```
composer require fluidtypo3/vhs
```

in a TYPO3 installation, or via [TER](https://extensions.typo3.org/extension/vhs).

## Settings

Although there are no static TypoScript files which can be included, VHS does
support a few key settings which are defined in TypoScript:

### Debug settings

* `plugin.tx_vhs.settings.debug = 1` can be used to enable general debugging,
  which causes Asset inclusions to be debugged right before inclusion in the
  page.

* `plugin.tx_vhs.settings.asset.debug = 1` can be used to enable debug output
  from individual Asset ViewHelper instances. Applies when a ViewHelper uses the
  "debug" parameter (where this is supported) and/or when
  `plugin.tx_vhs.settings.debug = 1`.

* `plugin.tx_vhs.settings.useDebugUtility` which causes VHS to use Extbase's
  DebugUtility to dump variables. If this setting is not defined a value of `1`
  is assumed.

## Assets

VHS includes a very useful feature that allows you to define assets (CSS,
JavaScript, etc.) in Fluid templates, PHP, and TypoScript. The traditional way
of including such assets in Fluid or elsewhere was that they were all used and
controlled differently and, probably worst of all, they were not all integration
friendly as assets could be modified with TypoScript. VHS Assets solves all
these problems.

### Asset examples

The following VHS ViewHelper usage:

```
<v:asset.script path="fileadmin/demo.js" />
```

is the exact same as this PHP call:

```
\FluidTYPO3\Vhs\Asset::createFromFile('fileadmin/demo.js');
```

which is a short form of:

```
\FluidTYPO3\Vhs\Asset::createFromSettings(array(
	'name' => 'demo',
	'path' => 'fileadmin/demo.js'
));
```

which is itself a short form of:

```
$asset = \FluidTYPO3\Vhs\Asset::getInstance();
// or alternatively, if this fits better in your other code:
$asset = $objectManager->get('FluidTYPO3\\Vhs\\Asset');
// then:
$asset->setName('demo');
$asset->setPath('fileadmin/demo.js');
$asset->finalize(); // manually created Assets must be finalized before they show up.
```

The PHP call above does the exact same as this TypoScript:

```
plugin.tx_vhs.settings.asset.demo.path = fileadmin/demo.js
```

which is a short form of:

```
plugin.tx_vhs.settings.asset.demo {
	name = demo
	path = fileadmin/demo.js
}
```

In summary: regardless of where and how you use VHS Assets, they always use the
same attributes, they always behave the same, support the same features (such as
dependency on other assets regardless of inclusion order and addressing assets
by a group name to affect multiple assets - and even rendering JavaScript and
CSS as if the file was a Fluid template).

The API for inclusion changes but the result is the same.

But the real benefit of VHS Assets comes in the form of the TypoScript
integration, which lets you override settings of individual assets (regardless
of how they were originally defined - Fluid, PHP, TypoScript) by setting their
attributes in TypoScript. This allows integrators to control every aspect of
every asset (but not the ones included in traditional ways) all the way down to
replacing the script source or CSS content that gets inserted or moving
JavaScript file(s) which used to be merged, to a new CDN server without even
breaking dependencies and execution order.

To affect VHS Assets through TypoScript, the following settings can be used:

### Asset settings

```
plugin.tx_vhs.settings.asset.ASSETNAME {
	content = Text # Text which overrides content
	path = FileReference # If set, turns Asset into a file inclusion
	name = Text a-zA-Z0-9_ # Can be used to change the name of an Asset on-the-fly, but watch out for dependencies
  external = Integer 0/1 # If set to `1` and `standalone`, includes the file as raw URL. If set to `1` and not `standalone` then downloads the file and merges it when building Assets
	overwrite = Integer 0/1 # If set to `1` this Asset is permitted to overwrite existing, identically named Assets
	dependencies = CSV # list of comma-separated Asset names upon which the current Asset depends; affects loading order
	group = Text a-zA-Z0-9_ # Group name, default "fluid". By grouping Assets the settings used on the group will apply to Assets
	debug = Integer 0/1 # If `1` enables debug output of each asset
	standalone = Integer 0/1 # If `1` instructs VHS to process this Asset as standalone, excluding it from merging
	async = Integer 0/1 # If 1, adds "async" attribute to script tag (only works when standalone is set and type is js)
	defer = Integer 0/1 # If 1, adds "defer" attribute to script tag (only works when standalone is set and type is js)
	movable = Integer 0/1 # If `0` prevents Assets from being included in the page footer. Used by style-type Assets. Default is `1` unless type is CSS which forces movable=0
	trim = Integer 0/1 # If `1` enables trimming of whitespace from beginning and end of lines when merging Assets
	namedChunks = Integer 0/1 # If `0` prevents Asset name from being inserted as comment above the Asset body in merged files
}
plugin.tx_vhs.settings.assetGroup.ASSETGROUPNAME {
	# this object supports the following properties only. When applied to a group the settings are used by each
	# Asset in that group, unless overridden directly in the Asset's attributes or through TypoScript as above.
	# SUPPORTED PROPERTIES: overwrite, dependencies, group, debug, standalone, allowMoveToFooter, trim and namedChunks
	# Please note: changing the "group" property changes the name of the group which means another group configuration
	# must be added which configures that group. Otherwise settings may be ignored.
}
plugin.tx_vhs.settings.asset {
	# this object supports every property which "assetGroup" supports except for the "group" and "dependencies" properties.
}
plugin.tx_vhs.assets {
	mergedAssetsUseHashedFilename = 0 # If set to a 1, Assets are merged into a file named using a hash if Assets' names.
	tagsAddSubresourceIntegrity = 0 # If set to 1 (weakest),2 or 3 (strongest), Vhs will generate and add the Subresource Integrity (SRI) for every included Asset.
}
```

## Secondary domain name for resources

You can configure VHS to write path prepends in two ways, one of which allows
you to create a so-called "cookie-free domain" on which requests will contain
fewer headers. Normally, setting `config.absRefPrefix` causes your resources'
paths to be prefixed with a domain, but using this approach will always prepend
a domain name which cannot be "cookie-free". VHS allows an alternative setting
for path prefix, which can be set to a secondary domain name (pointing to the
same virtual host or not) which sets no cookies, causing all asset tags to be
written with this prefix prepended:

```
plugin.tx_vhs.settings.prependPath = https://static.mydomain.com/
```

The setting affects *every* relative path resource ViewHelper (NB: this does not
include links!) in VHS, which is why it is not placed inside the "asset" scope.
If you need to output this prefix path in templates you can use the `v:page.staticPrefix`
ViewHelper - it accepts no arguments and only outputs the setting if it is set.
For example, using `f:image` will not prefix the image path but manually
creating an `<img />` tag and using `f:uri.image` as `src` argument will allow
you to prefix the path.
