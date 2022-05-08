.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-eliminate:

================
format.eliminate
================


Character/string/whitespace elimination ViewHelper

There is no example - each argument describes how it should be
used and arguments can be used individually or in any combination.

Arguments
=========


.. _format.eliminate_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String in which to perform replacement

.. _format.eliminate_casesensitive:

caseSensitive
-------------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Wether or not to perform case sensitive replacement

.. _format.eliminate_characters:

characters
----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Characters to remove. Array or string, i.e. {0: 'a', 1: 'b', 2: 'c'} or 'abc' to remove all occurrences of a, b and c

.. _format.eliminate_strings:

strings
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Strings to remove. Array or CSV, i.e. {0: 'foo', 1: 'bar'} or 'foo,bar' to remove all occorrences of foo and bar. If your strings overlap then place the longest match first

.. _format.eliminate_whitespace:

whitespace
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Eliminate ALL whitespace characters

.. _format.eliminate_whitespacebetweenhtmltags:

whitespaceBetweenHtmlTags
-------------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Eliminate ALL whitespace characters between HTML tags. Use this together with <f:format.raw>

.. _format.eliminate_tabs:

tabs
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Eliminate only tab whitespaces

.. _format.eliminate_unixbreaks:

unixBreaks
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Eliminate only UNIX line breaks

.. _format.eliminate_windowsbreaks:

windowsBreaks
-------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Eliminates only Windows carriage returns

.. _format.eliminate_digits:

digits
------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Eliminates all number characters (but not the dividers between floats converted to strings)

.. _format.eliminate_letters:

letters
-------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Eliminates all letters (non-numbers, non-whitespace, non-syntactical)

.. _format.eliminate_nonascii:

nonAscii
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Eliminates any ASCII char
