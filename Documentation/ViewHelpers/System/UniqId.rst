.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-system-uniqid:

=============
system.uniqId
=============


System: Unique ID
=================

Returns a unique ID based on PHP's uniqid-function.

Comes in useful when handling/generating html-element-IDs
for usage with JavaScript.

Arguments
=========


.. _system.uniqid_prefix:

prefix
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   An optional prefix for making sure it's unique across environments

.. _system.uniqid_moreentropy:

moreEntropy
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Add some pseudo random strings. Refer to uniqid()'s Reference.
