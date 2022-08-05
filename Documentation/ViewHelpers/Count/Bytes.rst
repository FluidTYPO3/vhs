.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-count-bytes:

===========
count.bytes
===========


Counts bytes (multibyte-safe) in a string.

Usage examples
--------------

::

    <v:count.bytes>{myString}</v:count.bytes> (output for example `42`

::

    {myString -> v:count.bytes()} when used inline

::

    <v:count.bytes string="{myString}" />

::

    {v:count.bytes(string: myString)}

Arguments
=========


.. _count.bytes_string:

string
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to count, if not provided as tag content

.. _count.bytes_encoding:

encoding
--------

:aspect:`DataType`
   string

:aspect:`Default`
   'UTF-8'

:aspect:`Required`
   false
:aspect:`Description`
   Character set encoding of string, e.g. UTF-8 or ISO-8859-1
