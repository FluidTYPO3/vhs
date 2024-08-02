:navigation-title: count.words
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-count-words:

==========================================
count.words ViewHelper `<vhs:count.words>`
==========================================


Counts words in a string.

Usage examples
--------------

::

    <v:count.words>{myString}</v:count.words> (output for example `42`

::

    {myString -> v:count.words()} when used inline

::

    <v:count.words string="{myString}" />

::

    {v:count.words(string: myString)}


.. _fluidtypo3-vhs-count-words_arguments:

Arguments
=========


.. _count.words_string:

string
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to count, if not provided as tag content
