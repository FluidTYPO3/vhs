.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-json-encode:

==================
format.json.encode
==================


JSON Encoding ViewHelper
========================

Returns a string containing the JSON representation of the argument.
The argument may be any of the following types:

- arrays, associative and traditional
- DomainObjects
- arrays containing DomainObjects
- ObjectStorage containing DomainObjects
- standard types (string, integer, boolean, float, NULL)
- DateTime including ones found as property values on DomainObjects

Recursion protection is enabled for DomainObjects with the option to
add a special marker (any variable type above also supported here)
which is inserted where an object which would cause recursion would
be placed.

Be specially careful when you JSON encode DomainObjects which have
recursive relations to itself using either 1:n or m:n - in this case
the one member of the converted relation will be whichever value you
specified as "recursionMarker" - or the default value, NULL. When
using the output of such conversion in JavaScript please make sure you
check the type before assuming that every member of a converted 1:n
or m:n recursive relation is in fact a JavaScript. Not doing so may
result in fatal JavaScript errors in the client browser.

Arguments
=========


.. _format.json.encode_value:

value
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to encode as JSON

.. _format.json.encode_usetraversablekeys:

useTraversableKeys
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, preserves keys from Traversables converted to arrays. Not recommended for ObjectStorages!

.. _format.json.encode_preventrecursion:

preventRecursion
----------------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, allows recursion to occur which could potentially be fatal to the output unless managed

.. _format.json.encode_recursionmarker:

recursionMarker
---------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Any value - string, integer, boolean, object or NULL - inserted instead of recursive instances of objects

.. _format.json.encode_datetimeformat:

dateTimeFormat
--------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   A date() format for DateTime values to JSON-compatible values. NULL means JS UNIXTIME (time()*1000)
