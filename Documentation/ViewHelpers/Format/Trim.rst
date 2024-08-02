:navigation-title: format.trim
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-trim:

==========================================
format.trim ViewHelper `<vhs:format.trim>`
==========================================


Trims $content by stripping off $characters (string list
of individual chars to strip off, default is all whitespaces).


.. _fluidtypo3-vhs-format-trim_arguments:

Arguments
=========


.. _format.trim_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to trim

.. _format.trim_characters:

characters
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   List of characters to trim, no separators, e.g. "abc123"
