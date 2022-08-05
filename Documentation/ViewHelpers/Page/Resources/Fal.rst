.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-resources-fal:

==================
page.resources.fal
==================


Page FAL resource ViewHelper.

Do not use the "uid" argument in the "Preview" section.
Instead, use the "record" argument and pass the entire record.
This bypasses visibility restrictions that normally apply when you attempt
to load a record by UID through TYPO3's PageRepository, which is what the
resource ViewHelpers do if you only pass uid.

Arguments
=========


.. _page.resources.fal_table:

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

.. _page.resources.fal_field:

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

.. _page.resources.fal_record:

record
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The actual record. Alternatively you can use the "uid" argument.

.. _page.resources.fal_uid:

uid
---

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of the record. Alternatively you can use the "record" argument.

.. _page.resources.fal_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, a template variable with this name containing the requested data will be inserted instead of returning it.

.. _page.resources.fal_asobjects:

asObjects
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Can be set to TRUE to return objects instead of file information arrays.

.. _page.resources.fal_limit:

limit
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Optional limit to the total number of records to render

.. _page.resources.fal_slide:

slide
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Enables Record Sliding - amount of levels which shall get walked up the rootline, including the current page. For infinite sliding (till the rootpage) set to -1. Only the first PID which has at minimum one record is used

.. _page.resources.fal_slidecollect:

slideCollect
------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, content is collected up the root line. If FALSE, only the first PID which has content is used. If greater than zero, this value overrides $slide.

.. _page.resources.fal_slidecollectreverse:

slideCollectReverse
-------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Normally when collecting records the elements from the actual page get shown on the top and those from the parent pages below those. You can invert this behaviour (actual page elements at bottom) by setting this flag))
