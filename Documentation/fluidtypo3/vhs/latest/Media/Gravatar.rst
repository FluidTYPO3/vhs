.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-gravatar:

==============
media.gravatar
==============


Renders Gravatar <img/> tag.

Arguments
=========


.. _media.gravatar_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _media.gravatar_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _media.gravatar_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _media.gravatar_class:

class
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSS class(es) for this element

.. _media.gravatar_dir:

dir
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)

.. _media.gravatar_id:

id
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Unique (in this file) identifier for this HTML element.

.. _media.gravatar_lang:

lang
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language for this element. Use short names specified in RFC 1766

.. _media.gravatar_style:

style
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Individual CSS styles for this element

.. _media.gravatar_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Tooltip text of element

.. _media.gravatar_accesskey:

accesskey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Keyboard shortcut to access this element

.. _media.gravatar_tabindex:

tabindex
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Specifies the tab order of this element

.. _media.gravatar_onclick:

onclick
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   JavaScript evaluated for the onclick event

.. _media.gravatar_email:

email
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Email address

.. _media.gravatar_size:

size
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Size in pixels, defaults to 80px [ 1 - 2048 ]

.. _media.gravatar_imageset:

imageSet
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Default image set to use. Possible values [ 404 | mm | identicon | monsterid | wavatar ]

.. _media.gravatar_maximumrating:

maximumRating
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Maximum rating (inclusive) [ g | pg | r | x ]

.. _media.gravatar_secure:

secure
------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If it is FALSE will return the un secure Gravatar domain (www.gravatar.com)
