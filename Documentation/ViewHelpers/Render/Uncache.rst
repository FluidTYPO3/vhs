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
   true
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

.. _render.uncache_persistpartialpaths:

persistPartialPaths
-------------------

:aspect:`DataType`
   mixed

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Normally, v:render.uncache will persist the partialRootPaths array that was active when the ViewHelperwas called, so the exact paths will be reused when rendering the uncached portion of the page output. This is done to ensure that even if you manually added some partial paths through some dynamic means (for example, based on a controller argument) then those paths would be used. However, in some cases this will be undesirable - namely when using a cache that is shared between multiple TYPO3 instances and each instance has a different path in the server's file system (e.g. load balanced setups). On such setups you should set persistPartialPaths="0" on this ViewHelper to prevent it from caching the resolved partialRootPaths. The ViewHelper will then instead use whichever partialRootPaths are configured for the extension that calls `v:render.uncache`. Note that when this is done, the special use case of dynamic or controller-overridden partialRootPaths is simply not supported.
