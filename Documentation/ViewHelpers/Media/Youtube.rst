.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-youtube:

=============
media.youtube
=============


Renders HTML code to embed a video from YouTube.

Arguments
=========


.. _media.youtube_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.youtube_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.youtube_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.youtube_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _media.youtube_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _media.youtube_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _media.youtube_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _media.youtube_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _media.youtube_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _media.youtube_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _media.youtube_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _media.youtube_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _media.youtube_videoid:

videoId
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   YouTube id of the video to embed.

.. _media.youtube_width:

width
-----

:aspect:`DataType`
   integer

:aspect:`Default`
   640

:aspect:`Required`
   false
:aspect:`Description`
   Width of the video in pixels. Defaults to 640 for 16:9 content.

.. _media.youtube_height:

height
------

:aspect:`DataType`
   integer

:aspect:`Default`
   385

:aspect:`Required`
   false
:aspect:`Description`
   Height of the video in pixels. Defaults to 385 for 16:9 content.

.. _media.youtube_autoplay:

autoplay
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Play the video automatically on load. Defaults to FALSE.

.. _media.youtube_legacycode:

legacyCode
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Whether to use the legacy flash video code.

.. _media.youtube_showrelated:

showRelated
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Whether to show related videos after playing.

.. _media.youtube_extendedprivacy:

extendedPrivacy
---------------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Whether to use cookie-less video player.

.. _media.youtube_hidecontrol:

hideControl
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Hide video player's control bar.

.. _media.youtube_hideinfo:

hideInfo
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Hide video player's info bar.

.. _media.youtube_enablejsapi:

enableJsApi
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Enable YouTube JavaScript API

.. _media.youtube_playlist:

playlist
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Comma seperated list of video IDs to be played.

.. _media.youtube_loop:

loop
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Play the video in a loop.

.. _media.youtube_start:

start
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Start playing after seconds.

.. _media.youtube_end:

end
---

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Stop playing after seconds.

.. _media.youtube_lighttheme:

lightTheme
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Use the YouTube player's light theme.

.. _media.youtube_videoquality:

videoQuality
------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Set the YouTube player's video quality (hd1080,hd720,highres,large,medium,small).

.. _media.youtube_windowmode:

windowMode
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Set the Window-Mode of the YouTube player (transparent,opaque). This is necessary for z-index handling in IE10/11.
