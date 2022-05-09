.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-menu-deferred:

=============
menu.deferred
=============


Page: Deferred menu rendering ViewHelper
========================================

Place this ViewHelper inside any other ViewHelper which
has been configured with the `deferred` attribute set to
TRUE - this will cause the output of the parent to only
contain the content of this ViewHelper.

Arguments
=========


.. _menu.deferred_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _menu.deferred_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _menu.deferred_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _menu.deferred_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _menu.deferred_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _menu.deferred_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _menu.deferred_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _menu.deferred_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _menu.deferred_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _menu.deferred_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _menu.deferred_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _menu.deferred_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _menu.deferred_forceclosingtag:

forceClosingTag
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, forces the created tag to use a closing tag. If FALSE, allows self-closing tags.

.. _menu.deferred_hideifempty:

hideIfEmpty
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Hide the tag completely if there is no tag content

.. _menu.deferred_contenteditable:

contenteditable
---------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the contents of the element are editable.

.. _menu.deferred_contextmenu:

contextmenu
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The value of the id attribute on the menu with which to associate the element as a context menu.

.. _menu.deferred_draggable:

draggable
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the element is draggable.

.. _menu.deferred_dropzone:

dropzone
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies what types of content can be dropped on the element, and instructs the UA about which actions to take with content when it is dropped on the element.

.. _menu.deferred_translate:

translate
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether an elements attribute values and contents of its children are to be translated when the page is localized, or whether to leave them unchanged.

.. _menu.deferred_spellcheck:

spellcheck
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the element represents an element whose contents are subject to spell checking and grammar checking.

.. _menu.deferred_hidden:

hidden
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the element represents an element that is not yet, or is no longer, relevant.

.. _menu.deferred_showaccessprotected:

showAccessProtected
-------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE links to access protected pages are always rendered regardless of user login status

.. _menu.deferred_classaccessprotected:

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

.. _menu.deferred_classaccessgranted:

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

.. _menu.deferred_useshortcutuid:

useShortcutUid
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, substitutes the link UID of a shortcut with the target page UID (and thus avoiding redirects) but does not change other data - which is done by using useShortcutData.

.. _menu.deferred_useshortcuttarget:

useShortcutTarget
-----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Optional param for using shortcut target instead of shortcut itself for current link

.. _menu.deferred_useshortcutdata:

useShortcutData
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Shortcut to set useShortcutTarget and useShortcutData simultaneously

.. _menu.deferred_tagname:

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

.. _menu.deferred_tagnamechildren:

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

.. _menu.deferred_entrylevel:

entryLevel
----------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Optional entryLevel TS equivalent of the menu

.. _menu.deferred_levels:

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

.. _menu.deferred_expandall:

expandAll
---------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE and $levels > 1 then expands all (not just the active) menu items which have submenus

.. _menu.deferred_classfirst:

classFirst
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name for the first menu elment

.. _menu.deferred_classlast:

classLast
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional class name for the last menu elment

.. _menu.deferred_classactive:

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

.. _menu.deferred_classcurrent:

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

.. _menu.deferred_classhassubpages:

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

.. _menu.deferred_substelementuid:

substElementUid
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Optional parameter for wrapping the link with the uid of the page

.. _menu.deferred_showhiddeninmenu:

showHiddenInMenu
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Include pages that are set to be hidden in menus

.. _menu.deferred_showcurrent:

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

.. _menu.deferred_linkcurrent:

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

.. _menu.deferred_linkactive:

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

.. _menu.deferred_titlefields:

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

.. _menu.deferred_includeanchortitle:

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

.. _menu.deferred_includespacers:

includeSpacers
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Wether or not to include menu spacers in the page select query

.. _menu.deferred_deferred:

deferred
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, does not output the tag content UNLESS a v:page.menu.deferred child ViewHelper is both used and triggered. This allows you to create advanced conditions while still using automatic rendering

.. _menu.deferred_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If used, stores the menu pages as an array in a variable named according to this value and renders the tag content - which means automatic rendering is disabled if this attribute is used

.. _menu.deferred_rootlineas:

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

.. _menu.deferred_excludepages:

excludePages
------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Page UIDs to exclude from the menu. Can be CSV, array or an object implementing Traversable.

.. _menu.deferred_forceabsoluteurl:

forceAbsoluteUrl
----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the menu will be rendered with absolute URLs

.. _menu.deferred_doktypes:

doktypes
--------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED: Please use typical doktypes for starting points like shortcuts.

.. _menu.deferred_divider:

divider
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional divider to insert between each menu item. Note that this does not mix well with automatic rendering due to the use of an ul > li structure
