.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-page-hassubpages:

==========================
condition.page.hasSubpages
==========================


Condition: Page has subpages
============================

A condition ViewHelper which renders the `then` child if
current page or page with provided UID has subpages. By default
disabled subpages are considered non existent which can be overridden
by setting $includeHidden to TRUE. To include pages that are hidden
in menus set $showHiddenInMenu to TRUE.

Arguments
=========


.. _condition.page.hassubpages_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.page.hassubpages_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.page.hassubpages_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Parent page to check

.. _condition.page.hassubpages_includehidden:

includeHidden
-------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED: Include hidden pages

.. _condition.page.hassubpages_includeaccessprotected:

includeAccessProtected
----------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Include access protected pages

.. _condition.page.hassubpages_includehiddeninmenu:

includeHiddenInMenu
-------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Include pages hidden in menu

.. _condition.page.hassubpages_showhiddeninmenu:

showHiddenInMenu
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED: Use includeHiddenInMenu
