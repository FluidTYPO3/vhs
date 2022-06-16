.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-resource-record-fal:

===================
resource.record.fal
===================


Resolve FAL relations and return file records.

Render a single image linked from a TCA record
==============================================

We assume that the table `tx_users` has a column `photo`, which is a FAL
relation field configured with
[`ExtensionManagementUtility::getFileFieldTCAConfig()`]
(https://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Inline/Index.html#file-abstraction-layer).
The template also has a `user` variable containing one of the table's
records.

At first, fetch the record and store it in a variable.
Then use `<f:image>` to render it:

::

    {v:resource.record.fal(table: 'tx_users', field: 'photo', record: user)
     -> v:iterator.first()
     -> v:variable.set(name: 'image')}
    <f:if condition="{image}">
      <f:image treatIdAsReference="1" src="{image.id}" title="{image.title}" alt="{image.alternative}"/>
    </f:if>

Use the `uid` attribute if you don't have a `record`.

Arguments
=========


.. _resource.record.fal_table:

table
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The table to lookup records.

.. _resource.record.fal_field:

field
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The field of the table associated to resources.

.. _resource.record.fal_record:

record
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The actual record. Alternatively you can use the "uid" argument.

.. _resource.record.fal_uid:

uid
---

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of the record. Alternatively you can use the "record" argument.

.. _resource.record.fal_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, a template variable with this name containing the requested data will be inserted instead of returning it.

.. _resource.record.fal_asobjects:

asObjects
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Can be set to TRUE to return objects instead of file information arrays.
