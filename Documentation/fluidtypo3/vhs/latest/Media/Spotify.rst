.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-spotify:

=============
media.spotify
=============


Renders HTML code to embed a Spotify play button.

Arguments
=========


.. _media.spotify_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.spotify_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.spotify_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.spotify_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _media.spotify_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _media.spotify_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _media.spotify_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _media.spotify_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _media.spotify_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _media.spotify_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _media.spotify_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _media.spotify_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _media.spotify_spotifyuri:

spotifyUri
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Spotify URI to create the play button for. Right click any song, album or playlist in Spotify and select Copy Spotify URI.

.. _media.spotify_width:

width
-----

:aspect:`DataType`
   mixed

:aspect:`Default`
   300

:aspect:`Required`
   false
:aspect:`Description`
   Width of the play button in pixels. Defaults to 300

.. _media.spotify_height:

height
------

:aspect:`DataType`
   mixed

:aspect:`Default`
   380

:aspect:`Required`
   false
:aspect:`Description`
   Height of the play button in pixels. Defaults to 380

.. _media.spotify_compact:

compact
-------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Whether to render the compact button with a fixed height of 80px.

.. _media.spotify_theme:

theme
-----

:aspect:`DataType`
   string

:aspect:`Default`
   'black'

:aspect:`Required`
   false
:aspect:`Description`
   Theme to use. Can be "black" or "white" and is not available in compact mode. Defaults to "black".

.. _media.spotify_view:

view
----

:aspect:`DataType`
   string

:aspect:`Default`
   'list'

:aspect:`Required`
   false
:aspect:`Description`
   View to use. Can be "list" or "coverart" and is not available in compact mode. Defaults to "list".
