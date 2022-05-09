.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-extension-path-resources:

========================
extension.path.resources
========================


Path: Relative Extension Resource Path
======================================

Site Relative path to Extension Resources/Public folder.

Arguments
=========


.. _extension.path.resources_extensionname:

extensionName
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name, in UpperCamelCase, of the extension to be checked

.. _extension.path.resources_path:

path
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional path to append after output of \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath
