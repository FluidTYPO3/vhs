.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-render-uncache:

==============
render.uncache
==============


Uncaches partials. Use like ``f:render``.
The partial will then be rendered each time.
Please be aware that this will impact render time.
Arguments must be serializable and will be cached.

Arguments
=========


.. _render.uncache_partial:

partial
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Reference to a partial.

.. _render.uncache_section:

section
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of section inside the partial to render.

.. _render.uncache_arguments:

arguments
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Arguments to pass to the partial.
