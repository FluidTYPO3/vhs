.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-source:

============
media.source
============


Used in conjuntion with the `v:media.PictureViewHelper`.
Please take a look at the `v:media.PictureViewHelper` documentation for more
information.

Arguments
=========


.. _media.source_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.source_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.source_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.source_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _media.source_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _media.source_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _media.source_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _media.source_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _media.source_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _media.source_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _media.source_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _media.source_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _media.source_media:

media
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Media query for which breakpoint this sources applies

.. _media.source_width:

width
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.

.. _media.source_height:

height
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.

.. _media.source_maxw:

maxW
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Maximum Width of the image. (no upscaling)

.. _media.source_maxh:

maxH
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Maximum Height of the image. (no upscaling)

.. _media.source_minw:

minW
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Minimum Width of the image.

.. _media.source_minh:

minH
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Minimum Height of the image.

.. _media.source_format:

format
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Format of the processed file - also determines the target file format. If blank, TYPO3/IM/GM default is taken into account.

.. _media.source_quality:

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

.. _media.source_relative:

relative
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Produce a relative URL instead of absolute
