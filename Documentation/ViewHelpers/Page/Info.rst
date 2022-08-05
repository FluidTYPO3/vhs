.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-info:

=========
page.info
=========


ViewHelper to access data of the current page record.

Does not work in the TYPO3 backend.

Arguments
=========


.. _page.info_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.

.. _page.info_pageuid:

pageUid
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   If specified, this UID will be used to fetch page data instead of using the current page.

.. _page.info_field:

field
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   If specified, only this field will be returned/assigned instead of the complete page record.
