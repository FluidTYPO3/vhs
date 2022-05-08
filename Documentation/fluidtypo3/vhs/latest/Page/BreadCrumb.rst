.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-breadcrumb:

===============
page.breadCrumb
===============


ViewHelper to make a breadcrumb link set from a pageUid, automatic or manual.

Arguments
=========


.. _page.breadcrumb_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _page.breadcrumb_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _page.breadcrumb_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _page.breadcrumb_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _page.breadcrumb_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _page.breadcrumb_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _page.breadcrumb_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _page.breadcrumb_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _page.breadcrumb_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _page.breadcrumb_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _page.breadcrumb_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _page.breadcrumb_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _page.breadcrumb_forceclosingtag:

forceClosingTag
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, forces the created tag to use a closing tag. If FALSE, allows self-closing tags.

.. _page.breadcrumb_hideifempty:

hideIfEmpty
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Hide the tag completely if there is no tag content

.. _page.breadcrumb_contenteditable:

contenteditable
---------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the contents of the element are editable.

.. _page.breadcrumb_contextmenu:

contextmenu
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The value of the id attribute on the menu with which to associate the element as a context menu.

.. _page.breadcrumb_draggable:

draggable
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the element is draggable.

.. _page.breadcrumb_dropzone:

dropzone
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies what types of content can be dropped on the element, and instructs the UA about which actions to take with content when it is dropped on the element.

.. _page.breadcrumb_translate:

translate
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether an elements attribute values and contents of its children are to be translated when the page is localized, or whether to leave them unchanged.

.. _page.breadcrumb_spellcheck:

spellcheck
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the element represents an element whose contents are subject to spell checking and grammar checking.

.. _page.breadcrumb_hidden:

hidden
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the element represents an element that is not yet, or is no longer, relevant.

.. _page.breadcrumb_showaccessprotected:

showAccessProtected
-------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE links to access protected pages are always rendered regardless of user login status

.. _page.breadcrumb_classaccessprotected:

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

.. _page.breadcrumb_classaccessgranted:

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

.. _page.breadcrumb_useshortcutuid:

useShortcutUid
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, substitutes the link UID of a shortcut with the target page UID (and thus avoiding redirects) but does not change other data - which is done by using useShortcutData.

.. _page.breadcrumb_useshortcuttarget:

useShortcutTarget
-----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Optional param for using shortcut target instead of shortcut itself for current link

.. _page.breadcrumb_useshortcutdata:

useShortcutData
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Shortcut to set useShortcutTarget and useShortcutData simultaneously

.. _page.breadcrumb_tagname:

tagName
-------

:aspect:`DataType`
   string

:aspect:`Default`
   'ul'

:aspect:`Required`
   false
:aspect:`Description`
   Tag name to use for enclosing container

.. _page.breadcrumb_tagnamechildren:

tagNameChildren
---------------

:aspect:`DataType`
   string

:aspect:`Default`
   'li'

:aspect:`Required`
   false
:aspect:`Description`
   Tag name to use for child nodes surrounding links. If set to "a" enables non-wrapping mode.

.. _page.breadcrumb_entrylevel:

entryLevel
----------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Optional entryLevel TS equivalent of the menu

.. _page.breadcrumb_levels:

levels
------

:aspect:`DataType`
   integer

:aspect:`Default`
   1

:aspect:`Required`
   false
:aspect:`Description`
   Number of levels to render - setting this to a number higher than 1 (one) will expand menu items that are active, to a depth of $levels starting from $entryLevel

.. _page.breadcrumb_expandall:

expandAll
---------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE and $levels > 1 then expands all (not just the active) menu items which have submenus

.. _page.breadcrumb_classfirst:

classFirst
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name for the first menu elment

.. _page.breadcrumb_classlast:

classLast
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name for the last menu elment

.. _page.breadcrumb_classactive:

classActive
-----------

:aspect:`DataType`
   string

:aspect:`Default`
   'active'

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name to add to active links

.. _page.breadcrumb_classcurrent:

classCurrent
------------

:aspect:`DataType`
   string

:aspect:`Default`
   'current'

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name to add to current link

.. _page.breadcrumb_classhassubpages:

classHasSubpages
----------------

:aspect:`DataType`
   string

:aspect:`Default`
   'sub'

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name to add to links which have subpages

.. _page.breadcrumb_substelementuid:

substElementUid
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Optional parameter for wrapping the link with the uid of the page

.. _page.breadcrumb_showhiddeninmenu:

showHiddenInMenu
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Include pages that are set to be hidden in menus

.. _page.breadcrumb_showcurrent:

showCurrent
-----------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, does not display the current page

.. _page.breadcrumb_linkcurrent:

linkCurrent
-----------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, does not wrap the current page in a link

.. _page.breadcrumb_linkactive:

linkActive
----------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, does not wrap with links the titles of pages that are active in the rootline

.. _page.breadcrumb_titlefields:

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

.. _page.breadcrumb_includeanchortitle:

includeAnchorTitle
------------------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, includes the page title as title attribute on the anchor.

.. _page.breadcrumb_includespacers:

includeSpacers
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Wether or not to include menu spacers in the page select query

.. _page.breadcrumb_deferred:

deferred
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, does not output the tag content UNLESS a v:page.menu.deferred child ViewHelper is both used and triggered. This allows you to create advanced conditions while still using automatic rendering

.. _page.breadcrumb_as:

as
--

:aspect:`DataType`
   string

:aspect:`Default`
   'breadcrumb'

:aspect:`Required`
   false
:aspect:`Description`
   If used, stores the menu pages as an array in a variable named after this value and renders the tag content. If the tag content is empty automatic rendering is triggered.

.. _page.breadcrumb_rootlineas:

rootLineAs
----------

:aspect:`DataType`
   string

:aspect:`Default`
   'rootLine'

:aspect:`Required`
   false
:aspect:`Description`
   If used, stores the menu root line as an array in a variable named according to this value and renders the tag content - which means automatic rendering is disabled if this attribute is used

.. _page.breadcrumb_excludepages:

excludePages
------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Page UIDs to exclude from the menu. Can be CSV, array or an object implementing Traversable.

.. _page.breadcrumb_forceabsoluteurl:

forceAbsoluteUrl
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the menu will be rendered with absolute URLs

.. _page.breadcrumb_doktypes:

doktypes
--------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED: Please use typical doktypes for starting points like shortcuts.

.. _page.breadcrumb_divider:

divider
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional divider to insert between each menu item. Note that this does not mix well with automatic rendering due to the use of an ul > li structure

.. _page.breadcrumb_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Optional parent page UID to use as top level of menu. If left out will be detected from rootLine using $entryLevel.

.. _page.breadcrumb_endlevel:

endLevel
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Optional deepest level of rendering. If left out all levels up to the current are rendered.
