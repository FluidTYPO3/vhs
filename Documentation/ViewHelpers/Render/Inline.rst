.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-render-inline:

=============
render.inline
=============


Render: Inline
==============

Render as string containing Fluid as if it were
part of the template currently being rendered.

Environment (template variables etc.) is cloned
but not re-merged after rendering, which means that
any and all changes in variables that happen while
rendering this inline code will be destroyed after
sub-rendering is finished.

Arguments
=========


.. _render.inline_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template code to render as Fluid (usually from a variable)

.. _render.inline_namespaces:

namespaces
----------

:aspect:`DataType`
   mixed

:aspect:`Default`
   array ()

:aspect:`Required`
   false
:aspect:`Description`
   Optional additional/overridden namespaces, ["ns" => "MyVendor\MyExt\ViewHelpers"]

.. _render.inline_onerror:

onError
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional error message to display if error occur while rendering. If NULL, lets the error Exception pass trough (and break rendering)

.. _render.inline_graceful:

graceful
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If forced to FALSE, errors are not caught but rather "transmitted" as every other error would be
