:navigation-title: media.image.height
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-image-height:

========================================================
media.image.height ViewHelper `<vhs:media.image.height>`
========================================================


Returns the height of the provided image file in pixels.


.. _fluidtypo3-vhs-media-image-height_arguments:

Arguments
=========


.. _media.image.height_src:

src
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   true
:aspect:`Description`
   Path to or id of the image file to determine info for. In case a FileReference is supplied, treatIdAsUid and treatIdAsReference will automatically be activated.

.. _media.image.height_treatidasuid:

treatIdAsUid
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the path argument is treated as a resource uid.

.. _media.image.height_treatidasreference:

treatIdAsReference
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the path argument is treated as a reference uid and will be resolved to a resource via sys_file_reference.
