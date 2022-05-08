.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-plaintext:

================
format.plaintext
================


Processes output as plaintext. Will trim whitespace off
each line that is provided, making display in a <pre>
work correctly indented even if the source is not.

Expects that you use f:format.htmlentities or similar
if you do not want HTML to be displayed as HTML, or
simply want it stripped out.

Arguments
=========


.. _format.plaintext_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Content to trim each line of text within
