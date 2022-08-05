.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-render-record:

=============
render.record
=============


ViewHelper used to render raw content records typically fetched
with `<v:content.get(column: '0', render: FALSE) />`.

If you simply want to render a content element, try `<v:content.render>`.

Arguments
=========


.. _render.record_column:

column
------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Column position number (colPos) of the column to render

.. _render.record_order:

order
-----

:aspect:`DataType`
   string

:aspect:`Default`
   'sorting'

:aspect:`Required`
   false
:aspect:`Description`
   Optional sort field of content elements - RAND() supported. Note that when sliding is enabled, the sorting will be applied to records on a per-page basis and not to the total set of collected records.

.. _render.record_sortdirection:

sortDirection
-------------

:aspect:`DataType`
   string

:aspect:`Default`
   'ASC'

:aspect:`Required`
   false
:aspect:`Description`
   Optional sort direction of content elements

.. _render.record_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   If set, selects only content from this page UID

.. _render.record_contentuids:

contentUids
-----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   If used, replaces all conditions with an "uid IN (1,2,3)" style condition using the UID values from this array

.. _render.record_sectionindexonly:

sectionIndexOnly
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, only renders/gets content that is marked as "include in section index"

.. _render.record_loadregister:

loadRegister
------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   List of LOAD_REGISTER variable

.. _render.record_render:

render
------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Render result

.. _render.record_hideuntranslated:

hideUntranslated
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, will NOT include elements which have NOT been translated, if current language is NOT the default language. Default is to show untranslated elements but never display the original if there is a translated version

.. _render.record_limit:

limit
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Optional limit to the total number of records to render

.. _render.record_slide:

slide
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Enables Record Sliding - amount of levels which shall get walked up the rootline, including the current page. For infinite sliding (till the rootpage) set to -1. Only the first PID which has at minimum one record is used

.. _render.record_slidecollect:

slideCollect
------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, content is collected up the root line. If FALSE, only the first PID which has content is used. If greater than zero, this value overrides $slide.

.. _render.record_slidecollectreverse:

slideCollectReverse
-------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Normally when collecting records the elements from the actual page get shown on the top and those from the parent pages below those. You can invert this behaviour (actual page elements at bottom) by setting this flag))

.. _render.record_record:

record
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Record to render
