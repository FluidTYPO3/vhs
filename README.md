TYPO3 extension VHS: Fluid ViewHelpers
======================================

	Collection of general purpose ViewHelpers usable in the Fluid templating engine
	that's bundled with the TYPO3 CMS.

## Overview

![ViewHelpers](https://raw.github.com/wiki/NamelessCoder/vhs/Images/Home/Overview.png)

*Grayed ViewHelpers are still incomplete, blue ViewHelpers are condition ViewHelpers which
mean they support usage of f:then and f:else as child tags - and the "then" and "else"
arguments, just like f:if does.*

## Wiki

A quick explanation can be read in this README.

Examples, tips and tricks can be found in the Wiki: https://github.com/NamelessCoder/vhs/wiki

## Installation

Download and install as TYPO3 extension. That's it. There are no configuration options
apart from the arguments which each ViewHelper accepts.

## Settings

Although there are no static TypoScript files which can be included, VHS does support a few
key settings which are defined in TypoScript:

### Debug settings

* `plugin.tx_vhs.settings.debug = 1` can be used to enable general debugging, which affects:
  - Asset inclusions are debugged right before inclusion in the page
* `plugin.tx_vhs.settings.asset.debug = 1` can be used to enable debug output from individual
  Asset ViewHelper instances. Applies when a ViewHelper uses the "debug" parameter (where this
  is supported) and/or when `plugin.tx_vhs.settings.debug = 1`.
* `plugin.tx_vhs.settings.useDebugUtility` which causes VHS to use Extbase's DebugUtility to
  dump variables. If this setting is not defined a value of `1` is assumed.

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
	allowMoveToFooter = Integer 0/1 # If `0` prevents Assets from being included in the page footer. Used by style-type Assets.
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
```

## Usage

To use the ViewHelpers in your Fluid templates simply add the namespace:

```xml
{namespace v=Tx_Vhs_ViewHelpers}
```

Using the namespace name "v" is not required but it is recommended. It's a single character
like the "f" namespace but is visually easy to distinguish from "f".

### A note about chaining inline syntax

Many of the VHS ViewHelpers make particular sense when used with their inline syntax.
Special care was taken to allow compact notations such as this:

```xml
<f:for each="{myQueryResult -> v:iterator.sort(sortBy: 'name')}" as="record">
	...
</f:for>
```

In the above case the sorted QueryResult is used only in the specific loop, preserving
the order of the original QueryResult.

Multiple chained syntax is also possible:

```xml
{bytes->v:math.division(b: 1024)->v:math.round()->f:format.number()} KB
```

Which will first take variable {bytes} and divide by 1024 to get a float KB size. Then round
that off to a whole integer and finally use f:format.number to ensure a localized display of
thousands and decimal separators. Which is fairly neat considering how such an operation would
appear if constructed in, for example, a Domain Object's getter method. The alternative would
be to create a highly customized "Format/KilobyteSizeViewHelper" or similar.

ViewHelpers for which this makes special sense are the formatting and math ViewHelpers. These
work well when applied in sequences such as the above or cases such as this:

```xml
{text -> v:format.trim() -> v:format.markdown()}
```

Naturally, the tag-based usage is supported the same way as the above but with one caveat
which one should always be aware of; that Fluid will render whitespace characters between
tags so that when you break your nested ViewHelper tags into multiple lines you will risk
causing data type mismatch errors - especially when using v:math which can result in
quite large expressions which would be very tempting to break into individual lines.

Regarding nesting of ViewHelpers you should note that v:format.trim does remove extra
whitespace - but it also converts the returned value to a string which could potentially be
misinterpreted when used as numeric values.

It is highly recommended to use the inline annotation when your return values have a specific
type before being output. Which is exactly the case when working with the v:math.* ViewHelpers.

## ViewHelper Group descriptions

### Condition

These ViewHelpers all use the AbstractConditionViewHelper base and supports the exact same usage
as f:if - which means that any ViewHelper in the Condition sub-scope as well as any ViewHelper
in the overview graphic which is marked with a question mark, supports usage such as:

```xml
<v:condition.frontend then="We're in FE context" />

<v:iterator.contains haystack="{arrayOrWhateverIterator}" needle="{specificObject}">
    <f:then>We've got a match!</f:then>
    <f:else>No match, sorry</f:else>
</v:iterator.contains>

<div class="{v:condition.frontend(then: 'fe-class')}">...</div>
```

### Extension

This group of ViewHelpers lets you query different aspects of a TYPO3 extension, such as wether
or not the extension is loaded (in which case you might want to make additional output) and
fetching paths for the extension such as the site-relative path to the extension directory or
an absolute path to the Resources folder of the extension.

### Form

This group is very small and currently provides just one ViewHelper-based feature: a form
select element which will let you manually render the <option> and <optgroup> tags and still
generate a valid form token to satisfy TYPO3 security.

### Format

As you would expect, this group contains various output formatting ViewHelpers such as Plaintext,
Tidy and Trim. Also contains formatters for URL parameters and a few Placeholder generators
which can output placeholder content useful when prototyping.

### Iterator

This group contains a range of ViewHelpers designed to work with Iterators (including arrays).
To name a few: sorting, conditions for existence of needle in haystack, next/previous conditions
which in addition also provides the next/previous object as a template varialbe available in the
tag's content including f:else/f:then children, custom loop (not foreach'ing an array but for'ing
until an upper limit) and implode/explode processors.

### Math

Contains all Math calculators, all of which support chained syntax and many of which are capable
of operating on single values as well as array inputs. Has calculators which can process a set
of numbers to calculate percentage of each number or of one number out of the set, which can be
useful when rendering statistical overviews (can alleviate the Controller hugely by using
chained syntax in the template for many consequetive calculations).

### Media

Contains ViewHelpers to access, check or otherwise process media (images, files etc.)

### Once

ViewHelpers which will only render their child content once, limited by various conditions such
as once per session, per cookie lifetime, per template context etc.

### Page

Contains many ViewHelpers to render various parts of a page - from menus, breadcrumbs, content
and header data. Contains a few nifty ViewHelpers to render specific content elements or place
content elements (or script tags) in the page footer by leveraging the PageRender.

### Random

Contains ViewHelpers to randomly create and select various types of content - generate random
numbers and strings, select random content and similar randomized purposes.

### Render

This group contains rendering logic ViewHelpers - which can render other Fluid templates with
custom variables, render Fluid saved in DB records, render sub-requests and implement caching.

### Security

Contains ViewHelpers to check for user login and/or group membership of users in both FE and BE
contexts. The Allow and Deny ViewHelpers are Condition ViewHelpers.

### Site

Contains ViewHelpers to interact with Site values such as site name and site url.

### Var

Contains VieWHelpers to modify, read, output and check variables used in the template.

## ViewHelper argument reference

Can be found online at:

http://fedext.net/vhs-viewhelpers/
