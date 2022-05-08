.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-resources:

==============
page.resources
==============


Page FAL resources ViewHelper.

Arguments
=========


.. _page.resources_table:

table
-----

:aspect:`DataType`
   string

:aspect:`Default`
   'pages'

:aspect:`Required`
   false
:aspect:`Description`
   The table to lookup records.

.. _page.resources_field:

field
-----

:aspect:`DataType`
   string

:aspect:`Default`
   'media'

:aspect:`Required`
   false
:aspect:`Description`
   The field of the table associated to resources.

.. _page.resources_record:

record
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The actual record. Alternatively you can use the "uid" argument.

.. _page.resources_uid:

uid
---

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of the record. Alternatively you can use the "record" argument.

.. _page.resources_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, a template variable with this name containing the requested data will be inserted instead of returning it.
