.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-debug:

=====
debug
=====


ViewHelper Debug ViewHelper (sic)
=================================

Debugs instances of other ViewHelpers and language
structures. Use in conjunction with other ViewHelpers
to inspect their current and possible arguments and
render their documentation:

::

    <v:debug><f:format.html>{variable}</f:format.html></v:debug>

Or the same expression in inline syntax:

::

    {variable -> f:format.html() -> v:debug()}

Can also be used to inspect `ObjectAccessor` instances
(e.g. variables you try to access) and rather than just
dumping the entire contents of the variable as is done
by `<f:debug />`, this ViewHelper makes a very simple
dump with a warning if the variable is not defined. If
an object is encountered (for example a domain object)
this ViewHelper will not dump the object but instead
will scan it for accessible properties (e.g. properties
which have a getter method!) and only present those
properties which can be accessed, along with the type
of variable that property currently contains:

::

    {domainObject -> v:debug()}

Assuming that `{domainObject}` is an instance of an
object which has two methods: `getUid()` and `getTitle()`,
debugging that instance will render something like this
in plain text:

::

    Path: {domainObject}
    Value type: object
    Accessible properties on {domainObject}:
       {form.uid} (integer)
       {form.title} (string)

The class itself can contain any number of protected
properties, but only those which have a getter method
can be accessed by Fluid and as therefore we only dump
those properties which you **can in fact access**.

Arguments
=========


This ViewHelper has no arguments.
