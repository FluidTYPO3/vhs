.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-resource-language:

=================
resource.language
=================


Resource: Language

Reads a certain language file with returning not just one single label,
but all the translated labels.

Examples
========

::

    <!-- Tag usage for force getting labels in a specific language (different to current is possible too) -->
    <v:resource.language extensionName="myext" path="Path/To/Locallang.xlf" languageKey="en"/>

::

    <!-- Tag usage for getting labels of current language -->
    <v:resource.language extensionName="myext" path="Path/To/Locallang.xlf"/>

Arguments
=========


.. _resource.language_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.

.. _resource.language_extensionname:

extensionName
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of the extension

.. _resource.language_path:

path
----

:aspect:`DataType`
   string

:aspect:`Default`
   'locallang.xlf'

:aspect:`Required`
   false
:aspect:`Description`
   Absolute or relative path to the locallang file

.. _resource.language_languagekey:

languageKey
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Key for getting translation of a different than current initialized language
