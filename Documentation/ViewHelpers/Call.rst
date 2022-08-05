.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-call:

====
call
====


Call ViewHelper
===============

Calls a method on an existing object. Usable as inline or tag.

Examples
========

::

    <!-- inline, useful as argument, for example in f:for -->
    {object -> v:call(method: 'toArray')}
    <!-- tag, useful to quickly output simple values -->
    <v:call object="{object}" method="unconventionalGetter" />
    <v:call method="unconventionalGetter">{object}</v:call>
    <!-- arguments for the method -->
    <v:call object="{object}" method="doSomethingWithArguments" arguments="{0: 'foo', 1: 'bar'}" />

Arguments
=========


.. _call_object:

object
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Instance to call method on

.. _call_method:

method
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of method to call on instance

.. _call_arguments:

arguments
---------

:aspect:`DataType`
   mixed

:aspect:`Default`
   array ()

:aspect:`Required`
   false
:aspect:`Description`
   Array of arguments if method requires arguments
