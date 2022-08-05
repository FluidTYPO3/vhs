.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-extension-path-absolute:

=======================
extension.path.absolute
=======================


Path: Absolute Extension Folder Path
====================================

Returns the absolute path to an extension folder.

Arguments
=========


.. _extension.path.absolute_extensionname:

extensionName
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name, in UpperCamelCase, of the extension to be checked

.. _extension.path.absolute_path:

path
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional path to append, second argument when calling \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath
