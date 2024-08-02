:navigation-title: iterator.shift
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-shift:

================================================
iterator.shift ViewHelper `<vhs:iterator.shift>`
================================================


Shifts the first value off $subject (but does not change $subject itself as array_shift would).


.. _fluidtypo3-vhs-iterator-shift_arguments:

Arguments
=========


.. _iterator.shift_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The input array/Traversable to shift

.. _iterator.shift_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
