.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-exists:

============
media.exists
============


File/Directory Exists Condition ViewHelper.

Arguments
=========


.. _media.exists_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _media.exists_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _media.exists_file:

file
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Filename which must exist to trigger f:then rendering

.. _media.exists_directory:

directory
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Directory which must exist to trigger f:then rendering
