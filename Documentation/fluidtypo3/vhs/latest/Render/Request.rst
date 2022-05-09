.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-render-request:

==============
render.request
==============


Render: Request
===============

Renders a sub-request to the desired Extension, Plugin,
Controller and action with the desired arguments.

Note: arguments must not be wrapped with the prefix used
in GET/POST parameters but must be provided as if the
arguments were sent directly to the Controller action.

Arguments
=========


.. _render.request_onerror:

onError
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional error message to display if error occur while rendering. If NULL, lets the error Exception pass trough (and break rendering)

.. _render.request_graceful:

graceful
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If forced to FALSE, errors are not caught but rather "transmitted" as every other error would be

.. _render.request_action:

action
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Controller action to call in request

.. _render.request_controller:

controller
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Controller name to call in request

.. _render.request_extensionname:

extensionName
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Extension name scope to use in request

.. _render.request_vendorname:

vendorName
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Vendor name scope to use in request. WARNING: only applies to TYPO3 versions below 10.4

.. _render.request_pluginname:

pluginName
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Plugin name scope to use in request

.. _render.request_arguments:

arguments
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Arguments to use in request
