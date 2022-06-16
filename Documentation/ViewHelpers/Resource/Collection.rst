.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-resource-collection:

===================
resource.collection
===================


Collection ViewHelper
=====================
This viewhelper returns a collection referenced by uid.
For more information look here:
http://docs.typo3.org/typo3cms/CoreApiReference/6.2/ApiOverview/Collections/Index.html#collections-api

Example
=======

::

    {v:resource.collection(uid:'123') -> v:var.set(name: 'someCollection')}

Arguments
=========


.. _resource.collection_uid:

uid
---

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   UID of the collection to be rendered
