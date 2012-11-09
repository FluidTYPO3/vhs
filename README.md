TYPO3 extension VHS: Fluid ViewHelpers
======================================

	Collection of general purpose ViewHelpers usable in the Fluid templating engine
	that's bundled with the TYPO3 CMS.

# Overview

![ViewHelpers](http://twitpic.com/show/full/bbs5pa.png)

*Checkmarked ViewHelpers are currently finished, others are still in progress. ViewHelpers
marked with a question mark icon are condition ViewHelpers which mean they support usage of
f:then and f:else as child tags - and the "then" and "else" arguments, just like f:if does.*

# Installation

Download and install as TYPO3 extension. That's it. There are no configuration options
apart from the arguments which each ViewHelper accepts.

# Usage

To use the ViewHelpers in your Fluid templates simply add the namespace:

```xml
{namespace v=Tx_Vhs_ViewHelpers}
```

Using the namespace name "v" is not required but it is recommended. It's a single character
like the "f" namespace but is visually easy to distinguish from "f".

## A note about chaining inline syntax

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
<a href="file.zip" title="{bytes->v:math.division(b: 1024)->v:math.round()->f:format.number()} KB">file</a>
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

# ViewHelper argument reference

Can be found online at:

http://fedext.net/vhs-viewhelpers/
