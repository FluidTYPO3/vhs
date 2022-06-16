.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-prepend:

==============
format.prepend
==============


Format: Prepend string content
==============================

Prepends one string on another. Although this task is very
easily done in standard Fluid - i.e. {add}{subject} - this
ViewHelper makes advanced chained inline processing possible:

::

    <!-- Adds 1H to DateTime, formats using timestamp input which requires prepended @ -->
    {dateTime.timestamp
        -> v:math.sum(b: 3600)
        -> v:format.prepend(add: '@')
        -> v:format.date(format: 'Y-m-d H:i')}
    <!-- You don't have to break the syntax into lines; done here for display only -->

Arguments
=========


.. _format.prepend_subject:

subject
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to prepend other string to

.. _format.prepend_add:

add
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to prepend
