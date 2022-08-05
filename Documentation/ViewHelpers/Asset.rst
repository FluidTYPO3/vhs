.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-asset:

=====
asset
=====


Basic Asset ViewHelper
======================

Places the contents of the asset (the tag body) directly
in the additional header content of the page. This most
basic possible version of an Asset has only the core
features shared by every Asset type:

- a "name" attribute which is required, identifying the Asset
  by a lowerCamelCase or lowercase_underscored value, your
  preference (but lowerCamelCase recommended for consistency).
- a "dependencies" attribute with a CSV list of other named
  Assets upon which the current Asset depends. When used, this
  Asset will be included after every asset listed as dependency.
- a "group" attribute which is optional and is used ty further
  identify the Asset as belonging to a particular group which
  can be suppressed or manipulated through TypoScript. For
  example, the default value is "fluid" and if TypoScript is
  used to exclude the group "fluid" then any Asset in that
  group will simply not be loaded.
- an "overwrite" attribute which if enabled causes any existing
  asset with the same name to be overwritten with the current
  Asset instead. If rendered in a loop only the last instance
  is actually used (this allows Assets in Partials which are
  rendered in an f:for loop).
- a "debug" property which enables output of the information
  used by the current Asset, with an option to force debug
  mode through TypoScript.
- additional properties which affect how the Asset is processed.
  For a full list see the argument descriptions; the same
  settings can be applied through TypoScript per-Asset, globally
  or per-Asset-group.

    Note: there are no static TypoScript templates for VHS but
    you will find a complete list in the README.md file in the
    root of the extension folder.

Arguments
=========


.. _asset_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Content to insert in header/footer

.. _asset_path:

path
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If not using tag content, specify path to file here

.. _asset_external:

external
--------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE and standalone, includes the file as raw URL. If TRUE and not standalone then downloads the file and merges it when building Assets

.. _asset_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional name of the content. If multiple occurrences of the same name happens, behavior is defined by argument "overwrite"

.. _asset_overwrite:

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

.. _asset_dependencies:

dependencies
------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   CSV list of other named assets upon which this asset depends. When included, this asset will always load after its dependencies

.. _asset_group:

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

.. _asset_debug:

debug
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, outputs information about this ViewHelper when the tag is used. Two master debug switches exist in TypoScript; see documentation about Page / Asset ViewHelper

.. _asset_standalone:

standalone
----------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, excludes this Asset from any concatenation which may be applied

.. _asset_rewrite:

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

.. _asset_fluid:

fluid
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, renders this (standalone or external) Asset as if it were a Fluid template, passing along values of the "variables" attribute or every available template variable if "variables" not specified

.. _asset_variables:

variables
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   An optional array of arguments which you use inside the Asset, be it standalone or inline. Use this argument to ensure your Asset filenames are only reused when all variables used in the Asset are the same

.. _asset_movable:

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

.. _asset_trim:

trim
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   DEPRECATED. Trim is no longer supported. Setting this to TRUE doesn't do anything.

.. _asset_namedchunks:

namedChunks
-----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If FALSE, hides the comment containing the name of each of Assets which is merged in a merged file. Disable to avoid a bit more output at the cost of transparency
