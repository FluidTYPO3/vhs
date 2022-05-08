.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-vimeo:

===========
media.vimeo
===========


Renders HTML code to embed a video from Vimeo.

Arguments
=========


.. _media.vimeo_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.vimeo_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.vimeo_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.vimeo_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _media.vimeo_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _media.vimeo_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _media.vimeo_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _media.vimeo_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _media.vimeo_title:

title
-----

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Show the title on the video. Defaults to TRUE.

.. _media.vimeo_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _media.vimeo_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _media.vimeo_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _media.vimeo_videoid:

videoId
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Vimeo ID of the video to embed.

.. _media.vimeo_width:

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

.. _media.vimeo_height:

height
------

:aspect:`DataType`
   integer

:aspect:`Default`
   360

:aspect:`Required`
   false
:aspect:`Description`
   Height of the video in pixels. Defaults to 360 for 16:9 content.

.. _media.vimeo_byline:

byline
------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Show the users byline on the video. Defaults to TRUE.

.. _media.vimeo_portrait:

portrait
--------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Show the users portrait on the video. Defaults to TRUE.

.. _media.vimeo_color:

color
-----

:aspect:`DataType`
   string

:aspect:`Default`
   '00adef'

:aspect:`Required`
   false
:aspect:`Description`
   Specify the color of the video controls. Defaults to 00adef. Make sure that you dont include the #.

.. _media.vimeo_autoplay:

autoplay
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Play the video automatically on load. Defaults to FALSE. Note that this wont work on some devices.

.. _media.vimeo_loop:

loop
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Play the video again when it reaches the end. Defaults to FALSE.

.. _media.vimeo_api:

api
---

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Set to TRUE to enable the Javascript API.

.. _media.vimeo_playerid:

playerId
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   An unique id for the player that will be passed back with all Javascript API responses.
