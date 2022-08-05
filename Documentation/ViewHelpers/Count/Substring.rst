.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-count-substring:

===============
count.substring
===============


Counts number of lines in a string.

Usage examples
--------------

::

    <v:count.substring string="{myString}">{haystack}</v:count.substring> (output for example `2`

::

    {haystack -> v:count.substring(string: myString)} when used inline

::

    <v:count.substring string="{myString}" haystack="{haystack}" />

::

    {v:count.substring(string: myString, haystack: haystack)}

Arguments
=========


.. _count.substring_haystack:

haystack
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to count substring in, if not provided as tag content

.. _count.substring_string:

string
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Substring to count occurrences of
