.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-random-string:

=============
random.string
=============


Random: String Generator
========================

Use either `minimumLength` / `maximumLength` or just `length`.

Specify the characters which can be randomized using `characters`.

Arguments
=========


.. _random.string_length:

length
------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Length of string to generate

.. _random.string_minimumlength:

minimumLength
-------------

:aspect:`DataType`
   integer

:aspect:`Default`
   32

:aspect:`Required`
   false
:aspect:`Description`
   Minimum length of string if random length

.. _random.string_maximumlength:

maximumLength
-------------

:aspect:`DataType`
   integer

:aspect:`Default`
   32

:aspect:`Required`
   false
:aspect:`Description`
   Minimum length of string if random length

.. _random.string_characters:

characters
----------

:aspect:`DataType`
   string

:aspect:`Default`
   '0123456789abcdef'

:aspect:`Required`
   false
:aspect:`Description`
   Characters to use in string
