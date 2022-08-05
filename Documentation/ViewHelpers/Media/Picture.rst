.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-picture:

=============
media.picture
=============


Renders a picture element with different images/sources for specific
media breakpoints

Example
=======

::

    <v:media.picture src="fileadmin/some-image.png" alt="Some Image" loading="lazy">
        <v:media.source media="(min-width: 1200px)" width="500c" height="500c" />
        <v:media.source media="(min-width: 992px)" width="300c" height="300c" />
        <v:media.source media="(min-width: 768px)" width="200c" height="200c" />
        <v:media.source width="80c" height="80c" />
    </v:media.picture>

Browser Support
===============

To have the widest Browser-Support you should consider using a polyfill like:
http://scottjehl.github.io/picturefill

Arguments
=========


.. _media.picture_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.picture_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.picture_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.picture_src:

src
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Path to the image or FileReference.

.. _media.picture_treatidasreference:

treatIdAsReference
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   When TRUE treat given src argument as sys_file_reference record.

.. _media.picture_alt:

alt
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text for the alt attribute.

.. _media.picture_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text for the title attribute.

.. _media.picture_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) to set.

.. _media.picture_loading:

loading
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Native lazy-loading for images. Can be "lazy", "eager" or "auto"
