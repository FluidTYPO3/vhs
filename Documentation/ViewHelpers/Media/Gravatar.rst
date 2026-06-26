:navigation-title: media.gravatar
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-gravatar:

================================================
media.gravatar ViewHelper `<vhs:media.gravatar>`
================================================


Renders Gravatar <img/> tag.


.. _fluidtypo3-vhs-media-gravatar_arguments:

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

.. _media.gravatar_email:

email
-----

:aspect:`DataType`
   string

:aspect:`Required`
   true
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
