.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-page-ischildpage:

==========================
condition.page.isChildPage
==========================


Condition: Page is child page
=============================

Condition ViewHelper which renders the `then` child if current
page or page with provided UID is a child of some other page in
the page tree. If $respectSiteRoot is set to TRUE root pages are
never considered child pages even if they are.

Arguments
=========


.. _condition.page.ischildpage_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.page.ischildpage_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.page.ischildpage_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Value to check

.. _condition.page.ischildpage_respectsiteroot:

respectSiteRoot
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Value to check
