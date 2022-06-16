.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-header-alternate:

=====================
page.header.alternate
=====================


Returns the all alternate urls.

Arguments
=========


.. _page.header.alternate_languages:

languages
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The languages (either CSV, array or implementing Traversable)

.. _page.header.alternate_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The page uid to check

.. _page.header.alternate_normalwhennolanguage:

normalWhenNoLanguage
--------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, a missing page overlay should be ignored

.. _page.header.alternate_addquerystring:

addQueryString
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the current query parameters will be kept in the URI
