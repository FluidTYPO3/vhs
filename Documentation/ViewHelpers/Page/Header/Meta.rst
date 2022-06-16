.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-header-meta:

================
page.header.meta
================


ViewHelper used to render a meta tag

If you use the ViewHelper in a plugin it has to be USER
not USER_INT, what means it has to be cached!

Arguments
=========


.. _page.header.meta_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _page.header.meta_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _page.header.meta_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _page.header.meta_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name property of meta tag

.. _page.header.meta_http-equiv:

http-equiv
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: http-equiv

.. _page.header.meta_property:

property
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property of meta tag

.. _page.header.meta_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Content of meta tag

.. _page.header.meta_scheme:

scheme
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: scheme

.. _page.header.meta_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: lang

.. _page.header.meta_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: dir
