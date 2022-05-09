.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-extension-loaded:

================
extension.loaded
================


Extension: Loaded (Condition) ViewHelper
========================================

Condition to check if an extension is loaded.

Example:
========

::

    {v:extension.loaded(extensionName: 'news', then: 'yes', else: 'no')}

::

    <v:extension.loaded extensionName="news">
        ...
    </v:extension.loaded>

Arguments
=========


.. _extension.loaded_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _extension.loaded_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _extension.loaded_extensionname:

extensionName
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of extension that must be loaded in order to evaluate as TRUE, UpperCamelCase
