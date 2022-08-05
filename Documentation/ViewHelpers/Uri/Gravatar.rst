.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-uri-gravatar:

============
uri.gravatar
============


Renders Gravatar URI.

Arguments
=========


.. _uri.gravatar_email:

email
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Email address

.. _uri.gravatar_size:

size
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Size in pixels, defaults to 80px [ 1 - 2048 ]

.. _uri.gravatar_imageset:

imageSet
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Default image set to use. Possible values [ 404 | mm | identicon | monsterid | wavatar ]

.. _uri.gravatar_maximumrating:

maximumRating
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Maximum rating (inclusive) [ g | pg | r | x ]

.. _uri.gravatar_secure:

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
