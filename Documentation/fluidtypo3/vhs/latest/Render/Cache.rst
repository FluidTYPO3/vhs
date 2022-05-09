.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-render-cache:

============
render.cache
============


Cache Rendering ViewHelper
==========================

Caches the child content (any type supported as long as it
can be serialized). Because of the added overhead you should
only use this if what you are caching is complex enough that
it performs many DB request (for example when displaying an
object with many lazy properties which don't load until the
template asks for the property value). In short, applies to
just about the same use cases as any other cache - but remember
that Fluid is already a very efficient rendering engine so don't
just assume that using the ViewHelper will increase performance
or decrease memory usage.

Works forcibly, i.e. can only re-render its content if the
cache is cleared. A CTRL+Refresh in the browser does nothing,
even if a BE user is logged in. Only use this ViewHelper around
content which you are absolutely sure it makes sense to cache
along with an identity - for example, if rendering an uncached
plugin which contains a Partial template that is in all aspects
just a solid-state HTML representation of something like a list
of current news.

The cache behind this ViewHelper is the Extbase object cache,
which is cleared when you clear the page content cache.

Do not use on form elements, it will invalidate the checksum.

Do not use around ViewHelpers which add header data or which
interact with the PageRenderer or other "live" objects; this
includes many of the VHS ViewHelpers!

Arguments
=========


.. _render.cache_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Content to be cached

.. _render.cache_identity:

identity
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Identity for cached entry

.. _render.cache_onerror:

onError
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional error message to display if error occur while rendering. If NULL, lets the error Exception pass trough (and break rendering)

.. _render.cache_graceful:

graceful
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If forced to FALSE, errors are not caught but rather "transmitted" as every other error would be
