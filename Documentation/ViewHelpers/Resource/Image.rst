.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-resource-image:

==============
resource.image
==============


ViewHelper to output or assign a image from FAL.

Arguments
=========


.. _resource.image_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _resource.image_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _resource.image_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _resource.image_identifier:

identifier
----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FAL combined identifiers (either CSV, array or implementing Traversable).

.. _resource.image_categories:

categories
----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The sys_category records to select the resources from (either CSV, array or implementing Traversable).

.. _resource.image_treatidasuid:

treatIdAsUid
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the identifier argument is treated as resource uids.

.. _resource.image_treatidasreference:

treatIdAsReference
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the identifier argument is treated as reference uids and will be resolved to resources via sys_file_reference.

.. _resource.image_relative:

relative
--------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE resource URIs are rendered absolute. URIs in backend mode are always absolute.

.. _resource.image_width:

width
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Width of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.

.. _resource.image_height:

height
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Height of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.

.. _resource.image_minwidth:

minWidth
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Minimum width of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.

.. _resource.image_minheight:

minHeight
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Minimum height of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.

.. _resource.image_maxwidth:

maxWidth
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Maximum width of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.

.. _resource.image_maxheight:

maxHeight
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Maximum height of the image. Numeric value in pixels or simple calculations. See imgResource.width for possible options.

.. _resource.image_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _resource.image_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _resource.image_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _resource.image_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _resource.image_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _resource.image_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _resource.image_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _resource.image_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _resource.image_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _resource.image_usemap:

usemap
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   A hash-name reference to a map element with which to associate the image.

.. _resource.image_ismap:

ismap
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Specifies that its img element provides access to a server-side image map.

.. _resource.image_alt:

alt
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Equivalent content for those who cannot process images or who have image loading disabled.

.. _resource.image_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, a template variable with this name containing the requested data will be inserted instead of returning it.
