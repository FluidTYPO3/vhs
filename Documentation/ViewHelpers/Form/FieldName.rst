.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-form-fieldname:

==============
form.fieldName
==============


Form Field Name View Helper

This viewhelper returns the properly prefixed name of the given
form field and generates the corresponding HMAC to allow posting
of dynamically added fields.

Arguments
=========


.. _form.fieldname_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of the form field to generate the HMAC for.

.. _form.fieldname_property:

property
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of object property. If used in conjunction with <f:form object="...">, "name" argument will be ignored.
