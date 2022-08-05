.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-append:

=============
format.append
=============


Format: Append string content
=============================

Appends a string after another string. Although this task is very
easily done in standard Fluid - i.e. {subject}{add} - this
ViewHelper makes advanced chained inline processing possible:

::

    <!-- useful when needing to chain string processing. Remove all "foo" and "bar"
         then add a text containing both "foo" and "bar", then format as HTML -->
    {text -> v:format.eliminate(strings: 'foo,bar')
          -> v:format.append(add: ' - my foo and bar are the only ones in this text.')
          -> f:format.html()}
    <!-- NOTE: you do not have to break the lines; done here only for presentation purposes -->

Makes no sense used as tag based ViewHelper:

::

    <!-- DO NOT USE - depicts COUNTERPRODUCTIVE usage! -->
    <v:format.append add="{f:translate(key: 're')}">{subject}</v:format.append>
    <!-- ... which is the exact same as ... -->
    <f:translate key="re" />{subject} <!-- OR --> {f:translate(key: 're')}{subject}

In other words: use this only when you do not have the option of
simply using {subject}{add}, i.e. in complex inline statements used
as attribute values on other ViewHelpers (where tag usage is undesirable).

Arguments
=========


.. _format.append_subject:

subject
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to append other string to

.. _format.append_add:

add
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to append
