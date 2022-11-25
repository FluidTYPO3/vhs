.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-content-resources-fal:

=====================
content.resources.fal
=====================


Content FAL relations ViewHelper

Render a single image in a content element
==========================================

We assume that the flux content element has an IRRE file field
`<flux:field.inline.fal name="settings.image">`.

The file data can be loaded and displayed with:

::

    {v:content.resources.fal(field: 'settings.image')
      -> v:iterator.first()
      -> v:variable.set(name: 'image')}
    <f:if condition="{image}">
      <f:image src="{image.uid}"/>
    </f:if>


Image preview in backend
========================

To load image data for the "Preview" section in the backend's page view,
you have to pass the `record` attribute:

::

    {v:content.resources.fal(field: 'settings.image', record: record)}

Arguments
=========


.. _content.resources.fal_table:

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

.. _content.resources.fal_field:

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

.. _content.resources.fal_record:

record
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The actual record. Alternatively you can use the "uid" argument.

.. _content.resources.fal_uid:

uid
---

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of the record. Alternatively you can use the "record" argument.

.. _content.resources.fal_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, a template variable with this name containing the requested data will be inserted instead of returning it.

.. _content.resources.fal_asobjects:

asObjects
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Can be set to TRUE to return objects instead of file information arrays.
