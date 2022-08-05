.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-content-info:

============
content.info
============


ViewHelper to access data of the current content element record.

Arguments
=========


.. _content.info_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.

.. _content.info_contentuid:

contentUid
----------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   If specified, this UID will be used to fetch content element data instead of using the current content element.

.. _content.info_field:

field
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, only this field will be returned/assigned instead of the complete content element record.
