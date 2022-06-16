.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-l:

=
l
=


L (localisation) ViewHelper
===========================

An extremely shortened and much more dev-friendly
alternative to f:translate. Automatically outputs
the name of the LLL reference if it is not found
and the default value is not set, making it much
easier to identify missing labels when translating.

Examples
========

::

    <v:l>some.label</v:l>
    <v:l key="some.label" />
    <v:l arguments="{0: 'foo', 1: 'bar'}">some.label</v:l>

Arguments
=========


.. _l_key:

key
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Translation Key

.. _l_default:

default
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If the given locallang key could not be found, this value is used. If this argument is not set, child nodes will be used to render the default

.. _l_htmlescape:

htmlEscape
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   TRUE if the result should be htmlescaped. This won't have an effect for the default value

.. _l_arguments:

arguments
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Arguments to be replaced in the resulting string

.. _l_extensionname:

extensionName
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   UpperCamelCased extension key (for example BlogExample)
