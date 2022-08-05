.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-system-timestamp:

================
system.timestamp
================


System: UNIX Timestamp
======================

Returns the current system UNIX timestamp as integer.
Useful combined with the Math group of ViewHelpers:

::

    <!-- adds exactly one hour to a DateTime and formats it -->
    <f:format.date format="H:i">{dateTime.timestamp -> v:math.sum(b: 3600)}</f:format.date>

Arguments
=========


This ViewHelper has no arguments.
