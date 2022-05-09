.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-menu-sub:

========
menu.sub
========


Page: Auto Sub Menu ViewHelper
==============================

Recycles the parent menu ViewHelper instance, resetting the
page UID used as starting point and repeating rendering of
the exact same tag content.

Used in custom menu rendering to indicate where a submenu is
to be rendered; accepts only a single argument called `pageUid`
which defines the new starting page UID that is used in the
recycled parent menu instance.

Arguments
=========


.. _menu.sub_pageuid:

pageUid
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Page UID to be overridden in the recycled rendering of the parent instance, if one exists
