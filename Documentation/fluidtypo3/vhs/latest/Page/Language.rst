.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-language:

=============
page.language
=============


Returns the current language from languages depending on l18n settings.

Arguments
=========


.. _page.language_languages:

languages
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The languages (either CSV, array or implementing Traversable)

.. _page.language_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The page uid to check

.. _page.language_normalwhennolanguage:

normalWhenNoLanguage
--------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, a missing page overlay should be ignored
