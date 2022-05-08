.. include:: /Includes.rst.txt

.. _configuration:

Configuration
=============

Although there are no static TypoScript files which can be included, VHS does
support a few key settings which are defined in TypoScript:

Debug settings
--------------

*  `plugin.tx_vhs.settings.debug = 1` can be used to enable general debugging,
   which causes Asset inclusions to be debugged right before inclusion in the
   page.

*  `plugin.tx_vhs.settings.asset.debug = 1` can be used to enable debug output
   from individual Asset ViewHelper instances. Applies when a ViewHelper uses the
   "debug" parameter (where this is supported) and/or when
   `plugin.tx_vhs.settings.debug = 1`.

*  `plugin.tx_vhs.settings.useDebugUtility` which causes VHS to use Extbase's
   DebugUtility to dump variables. If this setting is not defined a value of `1`
   is assumed.
