.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-daterange:

================
format.dateRange
================


Date range calculation/formatting ViewHelper
============================================

Uses DateTime and DateInterval operations to calculate a range
between two DateTimes.

Usages
------

- As formatter, the ViewHelper can output a string value such as
  "2013-04-30 - 2013-05-30" where you can configure both the start
  and end date (or their common) formats as well as the "glue"
  which binds the two dates together.
- As interval calculator, the ViewHelper can be used with a special
  "intervalFormat" which is a string used in the constructor method
  for the DateInterval class - for example, "P3M" to add three months.
  Used this way, you can specify the start date (or rely on the
  default "now" DateTime) and specify the "intervalFormat" to add
  your desired duration to your starting date and use that as end
  date. Without the "return" attribute, this mode simply outputs
  the formatted dates with interval deciding the end date.
- When used with the "return" attribute you can specify which type
  of data to return:
  - if "return" is "DateTime", a single DateTime instance is returned
    (which is the end date). Use this with a start date to return the
    DateTime corresponding to "intervalFormat" into the future/past.
  - if "return" is a string such as "w", "d", "h" etc. the corresponding
    counter value (weeks, days, hours etc.) is returned.
  - if "return" is an array of counter IDs, for example ["w", "d"],
    the corresponding counters from the range are returned as an array.

Note about LLL support and array consumers
------------------------------------------

When used with the "return" attribute and when this attribute is an
array, the output becomes suitable for consumption by f:translate, v:l
or f:format.sprintf for example - as the "arguments" attribute:

::

    <f:translate key="myDateDisplay"
        arguments="{v:format.dateRange(intervalFormat: 'P3W', return: {0: 'w', 1: 'd'})}"
    />

Which if "myDateDisplay" is a string such as "Deadline: %d week(s) and
%d day(s)" would output a result such as "Deadline: 4 week(s) and 2 day(s)".

    Tip: the values returned by this ViewHelper in both array and single
    value return modes, are also nicely consumable by the "math" suite
    of ViewHelpers, for example `v:math.division` would be able to divide
    number of days by two, three etc. to further divide the date range.

Arguments
=========


.. _format.daterange_start:

start
-----

:aspect:`DataType`
   mixed

:aspect:`Default`
   'now'

:aspect:`Required`
   false
:aspect:`Description`
   Start date which can be a DateTime object or a string consumable by DateTime constructor

.. _format.daterange_end:

end
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   End date which can be a DateTime object or a string consumable by DateTime constructor

.. _format.daterange_intervalformat:

intervalFormat
--------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Interval format consumable by DateInterval

.. _format.daterange_format:

format
------

:aspect:`DataType`
   string

:aspect:`Default`
   'Y-m-d'

:aspect:`Required`
   false
:aspect:`Description`
   Date format to apply to both start and end date

.. _format.daterange_startformat:

startFormat
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Date format to apply to start date

.. _format.daterange_endformat:

endFormat
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Date format to apply to end date

.. _format.daterange_glue:

glue
----

:aspect:`DataType`
   string

:aspect:`Default`
   '-'

:aspect:`Required`
   false
:aspect:`Description`
   Glue string to concatenate dates with

.. _format.daterange_spaceglue:

spaceGlue
---------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE glue string is surrounded with whitespace

.. _format.daterange_return:

return
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Return type; can be exactly "DateTime" to return a DateTime instance, a string like "w" or "d" to return weeks, days between the two dates - or an array of w, d, etc. strings to return the corresponding range count values as an array.
