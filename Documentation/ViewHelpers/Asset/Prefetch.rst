.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-asset-prefetch:

==============
asset.prefetch
==============


Asset DNS Prefetching ViewHelper
================================

Enables the special `<link rel="dns-prefetch" />` tag
which instructs the browser to start prefetching DNS
records for every domain listed in the `domains` attribute
of this ViewHelper. Prefetching starts as soon as the browser
becomes aware of the tag - to optimise even further, you may
wish to control the output buffer's size to deliver your site
HTML in chunks, the first chunk being the one containing this
ViewHelper.

Note that the web server daemon may send headers which prevent
this prefetching and that these headers can be added in many
ways. If prefetching does not work, you will need to inspect
the HTTP headers returned from the actual environment. Or you
may prefer to simply add `force="TRUE"` to this tag - but
beware that this will affect the entire document's behaviour,
not just for this particular set of domain prefetches. Once
force-enabled this setting cannot be disabled (unless done so
by manually adding an additional meta header tag as examplified
by the `build()` method.

Example usage:
==============

::

    <v:asset.prefetch domains="fedext.net,ajax.google.com" />

See: https://developer.mozilla.org/en-US/docs/Controlling_DNS_prefetching

Arguments
=========


.. _asset.prefetch_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Content to insert in header/footer

.. _asset.prefetch_path:

path
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If not using tag content, specify path to file here

.. _asset.prefetch_external:

external
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE and standalone, includes the file as raw URL. If TRUE and not standalone then downloads the file and merges it when building Assets

.. _asset.prefetch_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional name of the content. If multiple occurrences of the same name happens, behavior is defined by argument "overwrite"

.. _asset.prefetch_overwrite:

overwrite
---------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If set to FALSE and a relocated string with "name" already exists, does not overwrite the existing relocated string. Default behavior is to overwrite.

.. _asset.prefetch_dependencies:

dependencies
------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSV list of other named assets upon which this asset depends. When included, this asset will always load after its dependencies

.. _asset.prefetch_group:

group
-----

:aspect:`DataType`
   string

:aspect:`Default`
   'fluid'

:aspect:`Required`
   false
:aspect:`Description`
   Optional name of a logical group (created dynamically just by using the name) to which this particular asset belongs.

.. _asset.prefetch_debug:

debug
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, outputs information about this ViewHelper when the tag is used. Two master debug switches exist in TypoScript; see documentation about Page / Asset ViewHelper

.. _asset.prefetch_standalone:

standalone
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, excludes this Asset from any concatenation which may be applied

.. _asset.prefetch_rewrite:

rewrite
-------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, this Asset will be included as is without any processing of contained urls

.. _asset.prefetch_fluid:

fluid
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, renders this (standalone or external) Asset as if it were a Fluid template, passing along values of the "variables" attribute or every available template variable if "variables" not specified

.. _asset.prefetch_variables:

variables
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   An optional array of arguments which you use inside the Asset, be it standalone or inline. Use this argument to ensure your Asset filenames are only reused when all variables used in the Asset are the same

.. _asset.prefetch_movable:

movable
-------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, allows this Asset to be included in the document footer rather than the header. Should never be allowed for CSS.

.. _asset.prefetch_trim:

trim
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED. Trim is no longer supported. Setting this to TRUE doesn't do anything.

.. _asset.prefetch_namedchunks:

namedChunks
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, hides the comment containing the name of each of Assets which is merged in a merged file. Disable to avoid a bit more output at the cost of transparency

.. _asset.prefetch_domains:

domains
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Domain DNS names to prefetch. By default will add all sys_domain record DNS names

.. _asset.prefetch_protocol:

protocol
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional value of protocol as inserted in the resulting HREF value. If you experience problems with a non-protocol link, try enforcing http/https here

.. _asset.prefetch_protocolseparator:

protocolSeparator
-----------------

:aspect:`DataType`
   string

:aspect:`Default`
   '//'

:aspect:`Required`
   false
:aspect:`Description`
   If you do not enforce a particular protocol and wish to remove the double slashes from the hostname (your browser may not understand this!), set this attribute to an empty value (not-zero)

.. _asset.prefetch_force:

force
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, adds an additional meta header tag which forces prefetching to be enabled even if otherwise requested by the http daemon
