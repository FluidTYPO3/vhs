.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-replace:

==============
format.replace
==============


Replaces $substring in $content with $replacement.

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
   Content in which to perform replacement

.. _format.replace_substring:

substring
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Substring to replace

.. _format.replace_replacement:

replacement
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Replacement to insert

.. _format.replace_count:

count
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Maximum number of times to perform replacement

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
