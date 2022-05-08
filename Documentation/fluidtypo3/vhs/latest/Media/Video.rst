.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-video:

===========
media.video
===========


Renders HTML code to embed a HTML5 video player. NOTICE: This is
all HTML5 and won't work on browsers like IE8 and below. Include
some helper library like videojs.com if you need to suport those.
Source can be a single file, a CSV of files or an array of arrays
with multiple sources for different video formats. In the latter
case provide array keys 'src' and 'type'. Providing an array of
sources (even for a single source) is preferred as you can set
the correct mime type of the video which is otherwise guessed
from the filename's extension.

Arguments
=========


.. _media.video_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.video_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.video_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.video_src:

src
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Path to the media resource(s). Can contain single or multiple paths for videos/audio (either CSV, array or implementing Traversable).

.. _media.video_relative:

relative
--------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE media URIs are rendered absolute. URIs in backend mode are always absolute.

.. _media.video_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _media.video_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _media.video_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _media.video_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _media.video_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _media.video_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _media.video_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _media.video_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _media.video_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _media.video_forceclosingtag:

forceClosingTag
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, forces the created tag to use a closing tag. If FALSE, allows self-closing tags.

.. _media.video_hideifempty:

hideIfEmpty
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Hide the tag completely if there is no tag content

.. _media.video_contenteditable:

contenteditable
---------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the contents of the element are editable.

.. _media.video_contextmenu:

contextmenu
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The value of the id attribute on the menu with which to associate the element as a context menu.

.. _media.video_draggable:

draggable
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the element is draggable.

.. _media.video_dropzone:

dropzone
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies what types of content can be dropped on the element, and instructs the UA about which actions to take with content when it is dropped on the element.

.. _media.video_translate:

translate
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether an elements attribute values and contents of its children are to be translated when the page is localized, or whether to leave them unchanged.

.. _media.video_spellcheck:

spellcheck
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the element represents an element whose contents are subject to spell checking and grammar checking.

.. _media.video_hidden:

hidden
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the element represents an element that is not yet, or is no longer, relevant.

.. _media.video_width:

width
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Sets the width of the video player in pixels.

.. _media.video_height:

height
------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Sets the height of the video player in pixels.

.. _media.video_autoplay:

autoplay
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the video will start playing as soon as it is ready.

.. _media.video_controls:

controls
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that video controls should be displayed (such as a play/pause button etc).

.. _media.video_loop:

loop
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the video will start over again, every time it is finished.

.. _media.video_muted:

muted
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the audio output of the video should be muted.

.. _media.video_poster:

poster
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies an image to be shown while the video is downloading, or until the user hits the play button.

.. _media.video_preload:

preload
-------

:aspect:`DataType`
   string

:aspect:`Default`
   'auto'

:aspect:`Required`
   false
:aspect:`Description`
   Specifies if and how the author thinks the video should be loaded when the page loads. Can be "auto", "metadata" or "none".

.. _media.video_unsupported:

unsupported
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Add a message for old browsers like Internet Explorer 9 without video support.
