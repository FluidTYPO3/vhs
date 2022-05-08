.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-header-canonical:

=====================
page.header.canonical
=====================


Returns the current canonical url in a link tag.

Arguments
=========


.. _page.header.canonical_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _page.header.canonical_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _page.header.canonical_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _page.header.canonical_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _page.header.canonical_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _page.header.canonical_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _page.header.canonical_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _page.header.canonical_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _page.header.canonical_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _page.header.canonical_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _page.header.canonical_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _page.header.canonical_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _page.header.canonical_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The page uid to check

.. _page.header.canonical_querystringmethod:

queryStringMethod
-----------------

:aspect:`DataType`
   string

:aspect:`Default`
   'GET'

:aspect:`Required`
   false
:aspect:`Description`
   From which place to add parameters. Values: "GET", "POST" and "GET,POST". See https://docs.typo3.org/typo3cms/TyposcriptReference/Functions/Typolink/Index.html, addQueryString.method

.. _page.header.canonical_normalwhennolanguage:

normalWhenNoLanguage
--------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED: Visibility is now handled by core's typolink function.
