:navigation-title: format.hash
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-hash:

==========================================
format.hash ViewHelper `<vhs:format.hash>`
==========================================


Hashes a string.


.. _fluidtypo3-vhs-format-hash_arguments:

Arguments
=========


.. _format.hash_content:

content
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Content to hash

.. _format.hash_algorithm:

algorithm
---------

:aspect:`DataType`
   string

:aspect:`Default`
   'sha256'

:aspect:`Required`
   false
:aspect:`Description`
   Hashing algorithm to use (see http://php.net/manual/en/function.hash-algos.php for details)
