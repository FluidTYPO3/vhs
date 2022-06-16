.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-header-link:

================
page.header.link
================


ViewHelper used to render a link tag in the `<head>` section of the page.
If you use the ViewHelper in a plugin, the plugin and its action have to
be cached!

Arguments
=========


.. _page.header.link_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _page.header.link_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _page.header.link_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _page.header.link_rel:

rel
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: rel

.. _page.header.link_href:

href
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: href

.. _page.header.link_type:

type
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: type

.. _page.header.link_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: lang

.. _page.header.link_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Property: dir
