.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-image:

===========
media.image
===========


Renders an image tag for the given resource including all valid
HTML5 attributes. Derivates of the original image are rendered
if the provided (optional) dimensions differ.

## rendering responsive Images variants

You can use the srcset argument to generate several differently sized
versions of this image that will be added as a srcset argument to the img tag.
enter a list of widths in the srcset to genereate copies of the same crop +
ratio but in the specified widths. Put the width at the start that you want
to use as a fallback to be shown when no srcset functionality is supported.

Example
=======

::

    <v:media.image src="fileadmin/some-image.png" srcset="480,768,992,1200" />

Browser Support
===============

To have the widest Browser-Support you should consider using a polyfill like:
http://scottjehl.github.io/picturefill

Arguments
=========


.. _media.image_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.image_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.image_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.image_src:

src
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Path to the media resource(s). Can contain single or multiple paths for videos/audio (either CSV, array or implementing Traversable).

.. _media.image_relative:

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

.. _media.image_width:

width
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.

.. _media.image_height:

height
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.

.. _media.image_maxw:

maxW
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Maximum Width of the image. (no upscaling)

.. _media.image_maxh:

maxH
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Maximum Height of the image. (no upscaling)

.. _media.image_minw:

minW
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Minimum Width of the image.

.. _media.image_minh:

minH
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Minimum Height of the image.

.. _media.image_format:

format
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Format of the processed file - also determines the target file format. If blank, TYPO3/IM/GM default is taken into account.

.. _media.image_quality:

quality
-------

:aspect:`DataType`
   integer

:aspect:`Default`
   90

:aspect:`Required`
   false
:aspect:`Description`
   Quality of the processed image. If blank/not present falls back to the default quality defined in install tool.

.. _media.image_treatidasreference:

treatIdAsReference
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   When TRUE treat given src argument as sys_file_reference record. Applies only to TYPO3 6.x and above.

.. _media.image_canvaswidth:

canvasWidth
-----------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Width of an optional canvas to place the image on.

.. _media.image_canvasheight:

canvasHeight
------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Height of an optional canvas to place the image on.

.. _media.image_canvascolor:

canvasColor
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Background color of an optional canvas to place the image on (hex triplet).

.. _media.image_transparencycolor:

transparencyColor
-----------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Color to set transparent when using canvas feature (hex triplet).

.. _media.image_crop:

crop
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Information generated by the backend's graphical cropping UI

.. _media.image_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _media.image_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _media.image_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _media.image_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _media.image_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _media.image_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _media.image_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _media.image_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _media.image_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _media.image_usemap:

usemap
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   A hash-name reference to a map element with which to associate the image.

.. _media.image_ismap:

ismap
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that its img element provides access to a server-side image map.

.. _media.image_alt:

alt
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Equivalent content for those who cannot process images or who have image loading disabled.

.. _media.image_srcset:

srcset
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   List of width used for the srcset variants (either CSV, array or implementing Traversable)

.. _media.image_srcsetdefault:

srcsetDefault
-------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Default width to use as a fallback for browsers that don't support srcset
