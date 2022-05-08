.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-audio:

===========
media.audio
===========


Renders HTML code to embed a HTML5 audio player. NOTICE: This is
all HTML5 and won't work on browsers like IE8 and below. Include
some helper library like kolber.github.io/audiojs/ if you need to suport those.
Source can be a single file, a CSV of files or an array of arrays
with multiple sources for different audio formats. In the latter
case provide array keys 'src' and 'type'. Providing an array of
sources (even for a single source) is preferred as you can set
the correct mime type of the audio which is otherwise guessed
from the filename's extension.

Arguments
=========


.. _media.audio_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.audio_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.audio_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.audio_src:

src
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Path to the media resource(s). Can contain single or multiple paths for videos/audio (either CSV, array or implementing Traversable).

.. _media.audio_relative:

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

.. _media.audio_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _media.audio_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _media.audio_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _media.audio_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _media.audio_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _media.audio_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _media.audio_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _media.audio_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _media.audio_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _media.audio_forceclosingtag:

forceClosingTag
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, forces the created tag to use a closing tag. If FALSE, allows self-closing tags.

.. _media.audio_hideifempty:

hideIfEmpty
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Hide the tag completely if there is no tag content

.. _media.audio_contenteditable:

contenteditable
---------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the contents of the element are editable.

.. _media.audio_contextmenu:

contextmenu
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The value of the id attribute on the menu with which to associate the element as a context menu.

.. _media.audio_draggable:

draggable
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the element is draggable.

.. _media.audio_dropzone:

dropzone
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies what types of content can be dropped on the element, and instructs the UA about which actions to take with content when it is dropped on the element.

.. _media.audio_translate:

translate
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether an elements attribute values and contents of its children are to be translated when the page is localized, or whether to leave them unchanged.

.. _media.audio_spellcheck:

spellcheck
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies whether the element represents an element whose contents are subject to spell checking and grammar checking.

.. _media.audio_hidden:

hidden
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the element represents an element that is not yet, or is no longer, relevant.

.. _media.audio_width:

width
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Sets the width of the audio player in pixels.

.. _media.audio_height:

height
------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Sets the height of the audio player in pixels.

.. _media.audio_autoplay:

autoplay
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the audio will start playing as soon as it is ready.

.. _media.audio_controls:

controls
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that audio controls should be displayed (such as a play/pause button etc).

.. _media.audio_loop:

loop
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the audio will start over again, every time it is finished.

.. _media.audio_muted:

muted
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that the audio output of the audio should be muted.

.. _media.audio_poster:

poster
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies an image to be shown while the audio is downloading, or until the user hits the play button.

.. _media.audio_preload:

preload
-------

:aspect:`DataType`
   string

:aspect:`Default`
   'auto'

:aspect:`Required`
   false
:aspect:`Description`
   Specifies if and how the author thinks the audio should be loaded when the page loads. Can be "auto", "metadata" or "none".

.. _media.audio_unsupported:

unsupported
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Add a message for old browsers like Internet Explorer 9 without audio support.
