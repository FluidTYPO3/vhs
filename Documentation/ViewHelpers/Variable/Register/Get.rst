.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-variable-register-get:

=====================
variable.register.get
=====================


Variable\Register: Get
======================

ViewHelper used to read the value of a TSFE-register
Can be used to read names of variables which contain dynamic parts:

::

    <!-- if {variableName} is "Name", outputs value of {dynamicName} -->
    {v:variable.register.get(name: 'dynamic{variableName}')}

Arguments
=========


.. _variable.register.get_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of register
