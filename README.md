<img src="https://fluidtypo3.org/logo.svgz" width="100%" />

VHS: Fluid ViewHelpers
======================

> Collection of general purpose ViewHelpers usable in the Fluid templating engine
> that's bundled with the TYPO3 CMS.

[![Build Status](https://img.shields.io/travis/FluidTYPO3/vhs.svg?style=flat-square&label=package)](https://travis-ci.org/FluidTYPO3/vhs) [![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/vhs/development.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/vhs) [![Documentation](http://img.shields.io/badge/documentation-online-blue.svg?style=flat-square)](https://fluidtypo3.org/viewhelpers/vhs/master.html) [![Build Status](https://img.shields.io/travis/FluidTYPO3/fluidtypo3-testing.svg?style=flat-square&label=framework)](https://travis-ci.org/FluidTYPO3/fluidtypo3-testing/) [![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/fluidtypo3-testing/master.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/fluidtypo3-testing)

## Installation

Download and install as TYPO3 extension. That's it.

## Settings

Although there are no static TypoScript files which can be included, VHS does support a few key settings which are defined in TypoScript:

### Debug settings

* `plugin.tx_vhs.settings.debug = 1` can be used to enable general debugging, which causes Asset inclusions to be debugged right before inclusion in the page
* `plugin.tx_vhs.settings.asset.debug = 1` can be used to enable debug output from individual Asset ViewHelper instances. Applies when a ViewHelper uses the "debug" parameter (where this is supported) and/or when `plugin.tx_vhs.settings.debug = 1`.
* `plugin.tx_vhs.settings.useDebugUtility` which causes VHS to use Extbase's DebugUtility to dump variables. If this setting is not defined a value of `1` is assumed.

## Assets

VHS contains a highly useful feature which enables you to define Assets (CSS/JS/etc) in Fluid templates, PHP and TypoScript. What's different from the traditional ways of including such Assets (in Fluid or otherwise) all are used differently, controlled differently and probably worst of all, not all of them are integrator friendly (as in: allows Assets to be affected using TypoScript). VHS Assets solves all of this.

### Asset Examples

The following Fluid usage:

```
<v:asset.script path="fileadmin/demo.js" />
```

Is the exact same as ths PHP:

```
\FluidTYPO3\Vhs\Asset::createFromFile('fileadmin/demo.js');
```

Which is a short form of:

```
\FluidTYPO3\Vhs\Asset::createFromSettings(array(
	'name' => 'demo',
	'path' => 'fileadmin/demo.js'
));
```

Which is itself a short form of:

```
$asset = \FluidTYPO3\Vhs\Asset::getInstance();
// or alternatively, if this fits better in your other code:
$asset = $objectManager->get('FluidTYPO3\\Vhs\\Asset');
// then:
$asset->setName('demo');
$asset->setPath('fileadmin/demo.js');
$asset->finalize(); // manually created Assets must be finalized before they show up.
```

The PHP above does the exact same as this TypoScript:

```
plugin.tx_vhs.settings.asset.demo.path = fileadmin/demo.js
```

Which is a short form of:

```
plugin.tx_vhs.settings.asset.demo {
	name = demo
	path = fileadmin/demo.js
}
```

In summary: regardless of where and how you use VHS Assets, they always use the same attributes, they always behave the same, support the same features (such as dependency on other Assets regardless of inclusion order and addressing Assets by a group name to affect multiple Assets - and even rendering JS/CSS as if the file was a Fluid template).

The API for inclusion changes but the result is the same.

But the real benefit of VHS Assets comes in the form of the TypoScript integration, which lets you override settings of individual Assets (regardless of how they were originally defined - Fluid, PHP, TypoScript) by setting their attributes in TypoScript. This allows integrators to control every aspect of every Asset (but not the ones included in traditional ways) all the way down to replacing the script source or CSS content that gets inserted or moving JS file(s) which used by be merged, to a new CDN server without even breaking dependencies and execution order.

To affect VHS Assets through TypoScript, the following settings can be used:

### Asset settings

```javascript
plugin.tx_vhs.settings.asset.ASSETNAME {
	content = Text # Text which overrides content
	path = FileReference # If set, turns Asset into a file inclusion
	name = Text a-zA-Z0-9_ # Can be used to change the name of an Asset on-the-fly, but watch out for dependencies
	overwrite = Integer 0/1 # If set to `1` this Asset is permitted to overwrite existing, identically named Assets
	dependencies = CSV # list of comma-separated Asset names upon which the current Asset depends; affects loading order
	group = Text a-zA-Z0-9_ # Group name, default "fluid". By grouping Assets the settings used on the group will apply to Assets
	debug = Integer 0/1 # If `1` enables debug output of each asset
	standalone = Integer 0/1 # If `1` instructs VHS to process this Asset as standalone, excluding it from merging
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

You can configure VHS to write path prepends in two ways, one of which allows you to create a so-called "cookie-free domain" on which requests will contain fewer headers. Normally, setting `config.absRefPrefix` causes your resources' paths to be prefixed with a domain, but using this approach will always prepend a domain name which cannot be "cookie-free". VHS allows an alternative setting for path prefix, which can be set to a secondary domain name (pointing to the same virtual host or not) which sets no cookies, causing all asset tags to be written with this prefix prepended:

```javascript
plugin.tx_vhs.settings.prependPath = http://static.mydomain.com/
```

The setting affects *every* relative-path resource ViewHelper (NB: this does not include links!) in VHS, which is why it is not placed inside the "asset" scope. If you need to output this prefix path in templates you can use the `v:page.staticPrefix` ViewHelper - it accepts no arguments and only outputs the setting if it is set. For example, using `f:image` will not prefix the image path but manually creating an `<img />` tag and using `f:uri.image` as `src` argument will allow you to prefix the path.
