.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-variable-pregmatch:

==================
variable.pregMatch
==================


PregMatch regular expression ViewHelper
=======================================

Implementation of `preg_match' for Fluid.

Arguments
=========


.. _variable.pregmatch_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.

.. _variable.pregmatch_pattern:

pattern
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Regex pattern to match against

.. _variable.pregmatch_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   String to match with the regex pattern

.. _variable.pregmatch_global:

global
------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Match global
