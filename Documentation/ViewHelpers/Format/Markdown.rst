.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-markdown:

===============
format.markdown
===============


Markdown Transformation ViewHelper

Requires an installed "markdown" utility, the specific
implementation is less important since Markdown has no
configuration options. However, the utility or shell
scipt must:

- accept input from STDIN
- output to STDOUT
- place errors in STDERR
- be executable according to `open_basedir` and others
- exist within (one or more of) TYPO3's configured executable paths

In other words, *NIX standard behavior must be used.

See: http://daringfireball.net/projects/markdown

Arguments
=========


.. _format.markdown_text:

text
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Markdown to convert to HTML

.. _format.markdown_trim:

trim
----

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Trim content before converting

.. _format.markdown_htmlentities:

htmlentities
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If true, escapes converted HTML
