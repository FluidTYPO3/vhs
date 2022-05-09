.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-wordwrap:

===============
format.wordWrap
===============


Wordwrap: Wrap a string at provided character count
===================================================

Wraps a string to $limit characters and at $break character
while maintaining complete words. Concatenates the resulting
strings with $glue. Code is heavily inspired
by Codeigniter's word_wrap helper.

Arguments
=========


.. _format.wordwrap_subject:

subject
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text to wrap

.. _format.wordwrap_limit:

limit
-----

:aspect:`DataType`
   integer

:aspect:`Default`
   80

:aspect:`Required`
   false
:aspect:`Description`
   Maximum length of resulting parts after wrapping

.. _format.wordwrap_break:

break
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Character to wrap text at

.. _format.wordwrap_glue:

glue
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Character to concatenate parts with after wrapping
