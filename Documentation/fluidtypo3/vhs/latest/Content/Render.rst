.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-content-render:

==============
content.render
==============


ViewHelper used to render content elements in Fluid templates.

Render a single content element by its UID
==========================================

Let's assume that the variable `settings.element.uid` contains the uid
of a content element.
It can be rendered as follows:

::

    <v:content.render contentUids="{0: settings.element.uid}"/>

Arguments
=========


.. _content.render_column:

column
------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Column position number (colPos) of the column to render

.. _content.render_order:

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

.. _content.render_sortdirection:

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

.. _content.render_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   If set, selects only content from this page UID

.. _content.render_contentuids:

contentUids
-----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   If used, replaces all conditions with an "uid IN (1,2,3)" style condition using the UID values from this array

.. _content.render_sectionindexonly:

sectionIndexOnly
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, only renders/gets content that is marked as "include in section index"

.. _content.render_loadregister:

loadRegister
------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   List of LOAD_REGISTER variable

.. _content.render_render:

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

.. _content.render_hideuntranslated:

hideUntranslated
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, will NOT include elements which have NOT been translated, if current language is NOT the default language. Default is to show untranslated elements but never display the original if there is a translated version

.. _content.render_limit:

limit
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Optional limit to the total number of records to render

.. _content.render_slide:

slide
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Enables Record Sliding - amount of levels which shall get walked up the rootline, including the current page. For infinite sliding (till the rootpage) set to -1. Only the first PID which has at minimum one record is used

.. _content.render_slidecollect:

slideCollect
------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, content is collected up the root line. If FALSE, only the first PID which has content is used. If greater than zero, this value overrides $slide.

.. _content.render_slidecollectreverse:

slideCollectReverse
-------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Normally when collecting records the elements from the actual page get shown on the top and those from the parent pages below those. You can invert this behaviour (actual page elements at bottom) by setting this flag))

.. _content.render_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
