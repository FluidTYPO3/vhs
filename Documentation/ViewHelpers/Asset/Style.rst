.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-asset-style:

===========
asset.style
===========


Basic Style ViewHelper
======================

Allows inserting a `<link>` or `<style>` Asset. Settings
specify where to insert the Asset and how to treat it.

Arguments
=========


.. _asset.style_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Content to insert in header/footer

.. _asset.style_path:

path
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If not using tag content, specify path to file here

.. _asset.style_external:

external
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE and standalone, includes the file as raw URL. If TRUE and not standalone then downloads the file and merges it when building Assets

.. _asset.style_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional name of the content. If multiple occurrences of the same name happens, behavior is defined by argument "overwrite"

.. _asset.style_overwrite:

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

.. _asset.style_dependencies:

dependencies
------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSV list of other named assets upon which this asset depends. When included, this asset will always load after its dependencies

.. _asset.style_group:

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

.. _asset.style_debug:

debug
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, outputs information about this ViewHelper when the tag is used. Two master debug switches exist in TypoScript; see documentation about Page / Asset ViewHelper

.. _asset.style_standalone:

standalone
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, excludes this Asset from any concatenation which may be applied

.. _asset.style_rewrite:

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

.. _asset.style_fluid:

fluid
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, renders this (standalone or external) Asset as if it were a Fluid template, passing along values of the "variables" attribute or every available template variable if "variables" not specified

.. _asset.style_variables:

variables
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   An optional array of arguments which you use inside the Asset, be it standalone or inline. Use this argument to ensure your Asset filenames are only reused when all variables used in the Asset are the same

.. _asset.style_movable:

movable
-------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, allows this Asset to be included in the document footer rather than the header. Should never be allowed for CSS.

.. _asset.style_trim:

trim
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED. Trim is no longer supported. Setting this to TRUE doesn't do anything.

.. _asset.style_namedchunks:

namedChunks
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, hides the comment containing the name of each of Assets which is merged in a merged file. Disable to avoid a bit more output at the cost of transparency
