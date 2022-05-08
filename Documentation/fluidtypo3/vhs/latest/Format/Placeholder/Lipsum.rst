.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-placeholder-lipsum:

=========================
format.placeholder.lipsum
=========================


Lipsum ViewHelper

Renders Lorem Ipsum text according to provided arguments.

Arguments
=========


.. _format.placeholder.lipsum_lipsum:

lipsum
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional, custom lipsum source

.. _format.placeholder.lipsum_paragraphs:

paragraphs
----------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Number of paragraphs to output

.. _format.placeholder.lipsum_wordsperparagraph:

wordsPerParagraph
-----------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Number of words per paragraph

.. _format.placeholder.lipsum_skew:

skew
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Amount in number of words to vary the number of words per paragraph

.. _format.placeholder.lipsum_html:

html
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, renders output as HTML paragraph tags in the same way an RTE would

.. _format.placeholder.lipsum_parsefunctspath:

parseFuncTSPath
---------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If you want another parseFunc for HTML processing, enter the TS path here
