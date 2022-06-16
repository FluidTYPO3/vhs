.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-resource-record:

===============
resource.record
===============


Generic FAL resource ViewHelper

Arguments
=========


.. _resource.record_table:

table
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The table to lookup records.

.. _resource.record_field:

field
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The field of the table associated to resources.

.. _resource.record_record:

record
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The actual record. Alternatively you can use the "uid" argument.

.. _resource.record_uid:

uid
---

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of the record. Alternatively you can use the "record" argument.

.. _resource.record_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, a template variable with this name containing the requested data will be inserted instead of returning it.
