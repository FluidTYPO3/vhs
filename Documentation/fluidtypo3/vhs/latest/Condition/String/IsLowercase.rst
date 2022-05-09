.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-string-islowercase:

============================
condition.string.isLowercase
============================


Condition: String is lowercase
==============================

Condition ViewHelper which renders the `then` child if provided
string is lowercase. By default only the first letter is tested.
To test the full string set $fullString to TRUE.

Arguments
=========


.. _condition.string.islowercase_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.string.islowercase_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.string.islowercase_string:

string
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to check

.. _condition.string.islowercase_fullstring:

fullString
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Need
