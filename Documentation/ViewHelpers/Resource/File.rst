.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-resource-file:

=============
resource.file
=============


ViewHelper to output or assign FAL sys_file records.

Arguments
=========


.. _resource.file_additionalattributes:

additionalAttributes
--------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional tag attributes. They will be added directly to the resulting HTML tag.

.. _resource.file_data:

data
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional data-* attributes. They will each be added with a "data-" prefix.

.. _resource.file_aria:

aria
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Additional aria-* attributes. They will each be added with a "aria-" prefix.

.. _resource.file_identifier:

identifier
----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FAL combined identifiers (either CSV, array or implementing Traversable).

.. _resource.file_categories:

categories
----------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The sys_category records to select the resources from (either CSV, array or implementing Traversable).

.. _resource.file_treatidasuid:

treatIdAsUid
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the identifier argument is treated as resource uids.

.. _resource.file_treatidasreference:

treatIdAsReference
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the identifier argument is treated as reference uids and will be resolved to resources via sys_file_reference.

.. _resource.file_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.

.. _resource.file_onlyproperties:

onlyProperties
--------------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Whether to return only the properties array of the sys_file record and not the File object itself
