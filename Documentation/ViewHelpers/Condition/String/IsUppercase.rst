:navigation-title: condition.string.isUppercase
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-string-isuppercase:

============================================================================
condition.string.isUppercase ViewHelper `<vhs:condition.string.isUppercase>`
============================================================================


Condition: String is lowercase
==============================

Condition ViewHelper which renders the `then` child if provided
string is uppercase. By default only the first letter is tested.
To test the full string set $fullString to TRUE.


.. _fluidtypo3-vhs-condition-string-isuppercase_arguments:

Arguments
=========


.. _condition.string.isuppercase_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.string.isuppercase_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.string.isuppercase_string:

string
------

:aspect:`DataType`
   string

:aspect:`Required`
   true
:aspect:`Description`
   String to check

.. _condition.string.isuppercase_fullstring:

fullString
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Need
