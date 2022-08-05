.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-count-lines:

===========
count.lines
===========


Counts number of lines in a string.

Usage examples
--------------

::

    <v:count.lines>{myString}</v:count.lines> (output for example `42`

::

    {myString -> v:count.lines()} when used inline

::

    <v:count.lines string="{myString}" />

::

    {v:count.lines(string: myString)}

Arguments
=========


.. _count.lines_string:

string
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to count, if not provided as tag content
