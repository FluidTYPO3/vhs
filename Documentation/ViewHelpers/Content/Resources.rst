.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-content-resources:

=================
content.resources
=================


Resources ViewHelper

Loads FAL records associated with records of arbitrary types.

Arguments
=========


.. _content.resources_table:

table
-----

:aspect:`DataType`
   string

:aspect:`Default`
   'tt_content'

:aspect:`Required`
   false
:aspect:`Description`
   The table to lookup records.

.. _content.resources_field:

field
-----

:aspect:`DataType`
   string

:aspect:`Default`
   'image'

:aspect:`Required`
   false
:aspect:`Description`
   The field of the table associated to resources.

.. _content.resources_record:

record
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The actual record. Alternatively you can use the "uid" argument.

.. _content.resources_uid:

uid
---

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of the record. Alternatively you can use the "record" argument.

.. _content.resources_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, a template variable with this name containing the requested data will be inserted instead of returning it.
