.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-media-files:

===========
media.files
===========


Returns an array of files found in the provided path.

Arguments
=========


.. _media.files_path:

path
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Path to the folder containing the files to be listed.

.. _media.files_extensionlist:

extensionList
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   A comma seperated list of file extensions to pick up.

.. _media.files_prependpath:

prependPath
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If set to TRUE the path will be prepended to file names.

.. _media.files_order:

order
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If set to "mtime" sorts files by modification time or alphabetically otherwise.

.. _media.files_excludepattern:

excludePattern
--------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   A comma seperated list of filenames to exclude, no wildcards.
