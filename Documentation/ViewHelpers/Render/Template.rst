.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-render-template:

===============
render.template
===============


Render: Template
================

Render a template file (with arguments if desired).

Supports passing variables and controlling the format,
paths can be overridden and uses the same format as TS
settings a' la plugin.tx_myext.view, which means that
this can be done (from any extension, not just "foo")

::

    <v:render.template
     file="EXT:foo/Resources/Private/Templates/Action/Show.html"
     variables="{object: customLoadedObject}"
     paths="{v:variable.typoscript(path: 'plugin.tx_foo.view')}"
     format="xml" />

Which would render the "show" action's template from
EXT:foo using paths define in that extension's typoscript
but using a custom loaded object when rendering the template
rather than the object defined by the "Action" controller
of EXT:foo. The output would be in XML format and this
format would also be respected by Layouts and Partials
which are rendered from the Show.html template.

As such this is very similar to Render/RequestViewHelper
with two major differences:

1. A true ControllerContext is not present when rendering which
   means that links generated in the template should be made
   always including all parameters from ExtensionName over
   PluginName through the usual action etc.
2. The Controller from EXT:foo is not involved in any way,
   which means that any custom variables the particular
   template depends on must be added manually through
   the "variables" argument

Consider using Render/InlineViewHelper if you are rendering
templates from the same plugin.

Consider using Render/RequestViewHelper if you require a
completely isolated rendering identical to that which takes
place when rendering an Extbase plugin's content object.

Arguments
=========


.. _render.template_onerror:

onError
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional error message to display if error occur while rendering. If NULL, lets the error Exception pass trough (and break rendering)

.. _render.template_graceful:

graceful
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If forced to FALSE, errors are not caught but rather "transmitted" as every other error would be

.. _render.template_file:

file
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Path to template file, EXT:myext/... paths supported

.. _render.template_variables:

variables
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Optional array of template variables for rendering

.. _render.template_format:

format
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional format of the template(s) being rendered

.. _render.template_paths:

paths
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Optional array of arrays of layout and partial root paths, EXT:mypath/... paths supported
