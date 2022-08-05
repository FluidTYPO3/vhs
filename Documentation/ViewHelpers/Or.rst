.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-or:

==
or
==


If content is empty use alternative text (can also be LLL:labelname shortcut or LLL:EXT: file paths).

Arguments
=========


.. _or_content:

content
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Input to either use, if not empty

.. _or_alternative:

alternative
-----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Alternative if content is empty, can use LLL: shortcut

.. _or_arguments:

arguments
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Arguments to be replaced in the resulting string

.. _or_extensionname:

extensionName
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   UpperCamelCase extension name without vendor prefix
