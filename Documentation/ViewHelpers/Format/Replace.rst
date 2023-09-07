.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-replace:

==============
format.replace
==============


Replaces $substring in $content with $replacement.

Supports array as input substring/replacements and content.

When input substring/replacement is an array, both must be
the same length and must contain only strings.

When input content is an array, the search/replace is done
on every value in the input content array and the return
value will be an array of equal size as the input content
array but with all values search/replaced. All values in the
input content array must be strings.

Arguments
=========


.. _format.replace_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Content in which to perform replacement. Array supported.

.. _format.replace_substring:

substring
---------

:aspect:`DataType`
   string

:aspect:`Required`
   true
:aspect:`Description`
   Substring to replace. Array supported.

.. _format.replace_replacement:

replacement
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Replacement to insert. Array supported.

.. _format.replace_returncount:

returnCount
-----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, returns the number of replacements that were performed instead of returning output string. See also `v:count.substring`.

.. _format.replace_casesensitive:

caseSensitive
-------------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If true, perform case-sensitive replacement
