.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-languagemenu:

=================
page.languageMenu
=================


ViewHelper for rendering TYPO3 menus in Fluid
Require the extension static_info_table.

Arguments
=========


.. _page.languagemenu_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _page.languagemenu_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _page.languagemenu_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _page.languagemenu_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _page.languagemenu_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _page.languagemenu_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _page.languagemenu_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _page.languagemenu_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _page.languagemenu_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _page.languagemenu_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _page.languagemenu_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _page.languagemenu_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _page.languagemenu_tagname:

tagName
-------

:aspect:`DataType`
   string

:aspect:`Default`
   'ul'

:aspect:`Required`
   false
:aspect:`Description`
   Tag name to use for enclosing container, list and flags (not finished) only

.. _page.languagemenu_tagnamechildren:

tagNameChildren
---------------

:aspect:`DataType`
   string

:aspect:`Default`
   'li'

:aspect:`Required`
   false
:aspect:`Description`
   Tag name to use for child nodes surrounding links, list and flags only

.. _page.languagemenu_defaultisoflag:

defaultIsoFlag
--------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   ISO code of the default flag

.. _page.languagemenu_defaultlanguagelabel:

defaultLanguageLabel
--------------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Label for the default language

.. _page.languagemenu_order:

order
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Orders the languageIds after this list

.. _page.languagemenu_labeloverwrite:

labelOverwrite
--------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Overrides language labels

.. _page.languagemenu_hidenottranslated:

hideNotTranslated
-----------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Hides languageIDs which are not translated

.. _page.languagemenu_layout:

layout
------

:aspect:`DataType`
   string

:aspect:`Default`
   'flag,name'

:aspect:`Required`
   false
:aspect:`Description`
   How to render links when using autorendering. Possible selections: name,flag - use fx "name" or "flag,name" or "name,flag"

.. _page.languagemenu_usechash:

useCHash
--------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Use cHash for typolink. Has no effect on TYPO3 v9.5+

.. _page.languagemenu_flagpath:

flagPath
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Overwrites the path to the flag folder

.. _page.languagemenu_flagimagetype:

flagImageType
-------------

:aspect:`DataType`
   string

:aspect:`Default`
   'svg'

:aspect:`Required`
   false
:aspect:`Description`
   Sets type of flag image: png, gif, jpeg

.. _page.languagemenu_linkcurrent:

linkCurrent
-----------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Sets flag to link current language or not

.. _page.languagemenu_classcurrent:

classCurrent
------------

:aspect:`DataType`
   string

:aspect:`Default`
   'current'

:aspect:`Required`
   false
:aspect:`Description`
   Sets the class, by which the current language will be marked

.. _page.languagemenu_as:

as
--

:aspect:`DataType`
   string

:aspect:`Default`
   'languageMenu'

:aspect:`Required`
   false
:aspect:`Description`
   If used, stores the menu pages as an array in a variable named according to this value and renders the tag content - which means automatic rendering is disabled if this attribute is used

.. _page.languagemenu_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Optional page uid to use.

.. _page.languagemenu_configuration:

configuration
-------------

:aspect:`DataType`
   mixed

:aspect:`Default`
   array ()

:aspect:`Required`
   false
:aspect:`Description`
   Additional typoLink configuration

.. _page.languagemenu_excludequeryvars:

excludeQueryVars
----------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Comma-separate list of variables to exclude

.. _page.languagemenu_languages:

languages
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Array, CSV or Traversable containing UIDs of languages to render
