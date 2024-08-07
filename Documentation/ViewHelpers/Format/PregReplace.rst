:navigation-title: format.pregReplace
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-pregreplace:

========================================================
format.pregReplace ViewHelper `<vhs:format.pregReplace>`
========================================================


PregReplace regular expression ViewHelper
=========================================

Implementation of `preg_replace` for Fluid.


.. _fluidtypo3-vhs-format-pregreplace_arguments:

Arguments
=========


.. _format.pregreplace_subject:

subject
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to match with the regex pattern or patterns

.. _format.pregreplace_pattern:

pattern
-------

:aspect:`DataType`
   string

:aspect:`Required`
   true
:aspect:`Description`
   Regex pattern to match against

.. _format.pregreplace_replacement:

replacement
-----------

:aspect:`DataType`
   string

:aspect:`Required`
   true
:aspect:`Description`
   String to replace matches with

.. _format.pregreplace_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
