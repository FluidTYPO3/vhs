.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-variable-extensionconfiguration:

===============================
variable.extensionConfiguration
===============================


ExtConf ViewHelper
==================

Reads settings from ext_conf_template.txt

Examples
========

::

    {v:variable.extensionConfiguration(extensionKey:'foo',path:'bar.baz')}

Returns setting `bar.baz` from extension 'foo' located in `ext_conf_template.txt`.

Arguments
=========


.. _variable.extensionconfiguration_extensionkey:

extensionKey
------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Extension key (lowercase_underscored format) to read configuration from

.. _variable.extensionconfiguration_path:

path
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Configuration path to read - if NULL, returns all configuration as array
