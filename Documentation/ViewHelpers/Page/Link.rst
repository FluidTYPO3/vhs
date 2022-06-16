.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-link:

=========
page.link
=========


Page: Link ViewHelper
=====================

Viewhelper for rendering page links

This viewhelper behaves identically to Fluid's link viewhelper
except for it fetches the title of the provided page UID and inserts
it as linktext if that is omitted. The link will not render at all
if the requested page is not translated in the current language.

::

    Automatic linktext: <v:page.link pageUid="UID" />
    Manual linktext:    <v:page.link pageUid="UID">linktext</v:page.link>

Arguments
=========


.. _page.link_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _page.link_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _page.link_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _page.link_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _page.link_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _page.link_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _page.link_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _page.link_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _page.link_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _page.link_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _page.link_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _page.link_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _page.link_showaccessprotected:

showAccessProtected
-------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE links to access protected pages are always rendered regardless of user login status

.. _page.link_classaccessprotected:

classAccessProtected
--------------------

:aspect:`DataType`
   string

:aspect:`Default`
   'protected'

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name to add to links which are access protected

.. _page.link_classaccessgranted:

classAccessGranted
------------------

:aspect:`DataType`
   string

:aspect:`Default`
   'access-granted'

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name to add to links which are access protected but access is actually granted

.. _page.link_useshortcutuid:

useShortcutUid
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, substitutes the link UID of a shortcut with the target page UID (and thus avoiding redirects) but does not change other data - which is done by using useShortcutData.

.. _page.link_useshortcuttarget:

useShortcutTarget
-----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Optional param for using shortcut target instead of shortcut itself for current link

.. _page.link_useshortcutdata:

useShortcutData
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Shortcut to set useShortcutTarget and useShortcutData simultaneously

.. _page.link_target:

target
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Target of link

.. _page.link_rel:

rel
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the relationship between the current document and the linked document

.. _page.link_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   UID of the page to create the link and fetch the title for.

.. _page.link_additionalparams:

additionalParams
----------------

:aspect:`DataType`
   mixed

:aspect:`Default`
   array ()

:aspect:`Required`
   false
:aspect:`Description`
   Query parameters to be attached to the resulting URI

.. _page.link_pagetype:

pageType
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Type of the target page. See typolink.parameter

.. _page.link_nocache:

noCache
-------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   When TRUE disables caching for the target page. You should not need this.

.. _page.link_nocachehash:

noCacheHash
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   When TRUE supresses the cHash query parameter created by TypoLink. You should not need this. Has no effect on TYPO3v11 and above.

.. _page.link_section:

section
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The anchor to be added to the URI

.. _page.link_linkaccessrestrictedpages:

linkAccessRestrictedPages
-------------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED: Use showAccessProtected instead.

.. _page.link_absolute:

absolute
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   When TRUE, the URI of the rendered link is absolute

.. _page.link_addquerystring:

addQueryString
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   When TRUE, the current query parameters will be kept in the URI

.. _page.link_argumentstobeexcludedfromquerystring:

argumentsToBeExcludedFromQueryString
------------------------------------

:aspect:`DataType`
   mixed

:aspect:`Default`
   array ()

:aspect:`Required`
   false
:aspect:`Description`
   Arguments to be removed from the URI. Only active if $addQueryString = TRUE

.. _page.link_titlefields:

titleFields
-----------

:aspect:`DataType`
   string

:aspect:`Default`
   'nav_title,title'

:aspect:`Required`
   false
:aspect:`Description`
   CSV list of fields to use as link label - default is "nav_title,title", change to for example "tx_myext_somefield,subtitle,nav_title,title". The first field that contains text will be used. Field value resolved AFTER page field overlays.

.. _page.link_pagetitleas:

pageTitleAs
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   When rendering child content, supplies page title as variable.
