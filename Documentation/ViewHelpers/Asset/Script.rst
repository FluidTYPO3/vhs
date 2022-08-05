.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-asset-script:

============
asset.script
============


Basic Script ViewHelper
=======================

Allows inserting a `<script>` Asset. Settings specify
where to insert the Asset and how to treat it.

Arguments
=========


.. _asset.script_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Content to insert in header/footer

.. _asset.script_path:

path
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If not using tag content, specify path to file here

.. _asset.script_external:

external
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE and standalone, includes the file as raw URL. If TRUE and not standalone then downloads the file and merges it when building Assets

.. _asset.script_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional name of the content. If multiple occurrences of the same name happens, behavior is defined by argument "overwrite"

.. _asset.script_overwrite:

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

.. _asset.script_dependencies:

dependencies
------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSV list of other named assets upon which this asset depends. When included, this asset will always load after its dependencies

.. _asset.script_group:

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

.. _asset.script_debug:

debug
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, outputs information about this ViewHelper when the tag is used. Two master debug switches exist in TypoScript; see documentation about Page / Asset ViewHelper

.. _asset.script_standalone:

standalone
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, excludes this Asset from any concatenation which may be applied

.. _asset.script_rewrite:

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

.. _asset.script_fluid:

fluid
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, renders this (standalone or external) Asset as if it were a Fluid template, passing along values of the "variables" attribute or every available template variable if "variables" not specified

.. _asset.script_variables:

variables
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   An optional array of arguments which you use inside the Asset, be it standalone or inline. Use this argument to ensure your Asset filenames are only reused when all variables used in the Asset are the same

.. _asset.script_movable:

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

.. _asset.script_trim:

trim
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED. Trim is no longer supported. Setting this to TRUE doesn't do anything.

.. _asset.script_namedchunks:

namedChunks
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, hides the comment containing the name of each of Assets which is merged in a merged file. Disable to avoid a bit more output at the cost of transparency

.. _asset.script_async:

async
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, adds "async" attribute to script tag (only works when standalone is set)

.. _asset.script_defer:

defer
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, adds "defer" attribute to script tag (only works when standalone is set)
