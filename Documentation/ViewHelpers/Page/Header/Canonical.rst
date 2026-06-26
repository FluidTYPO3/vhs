:navigation-title: page.header.canonical
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-header-canonical:

==============================================================
page.header.canonical ViewHelper `<vhs:page.header.canonical>`
==============================================================


Returns the current canonical url in a link tag.


.. _fluidtypo3-vhs-page-header-canonical_arguments:

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
