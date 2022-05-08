.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-image-width:

=================
media.image.width
=================


Returns the width of the provided image file in pixels.

Arguments
=========


.. _media.image.width_src:

src
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Path to or id of the image file to determine info for. In case a FileReference is supplied, treatIdAsUid and treatIdAsReference will automatically be activated.

.. _media.image.width_treatidasuid:

treatIdAsUid
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the path argument is treated as a resource uid.

.. _media.image.width_treatidasreference:

treatIdAsReference
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the path argument is treated as a reference uid and will be resolved to a resource via sys_file_reference.
