:navigation-title: media.image.mimetype
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-image-mimetype:

============================================================
media.image.mimetype ViewHelper `<vhs:media.image.mimetype>`
============================================================


Returns the mimetype of the provided image file.


.. _fluidtypo3-vhs-media-image-mimetype_arguments:

Arguments
=========


.. _media.image.mimetype_src:

src
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   true
:aspect:`Description`
   Path to or id of the image file to determine info for. In case a FileReference is supplied, treatIdAsUid and treatIdAsReference will automatically be activated.

.. _media.image.mimetype_treatidasuid:

treatIdAsUid
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the path argument is treated as a resource uid.

.. _media.image.mimetype_treatidasreference:

treatIdAsReference
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the path argument is treated as a reference uid and will be resolved to a resource via sys_file_reference.
