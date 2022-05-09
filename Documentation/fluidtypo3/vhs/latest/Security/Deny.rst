.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-security-deny:

=============
security.deny
=============


Security: Deny
==============

Denies access to the child content based on given arguments.
The ViewHelper is a condition based ViewHelper which means it
supports the `f:then` and `f:else` child nodes.

Is the mirror opposite of `v:security.allow`.

Arguments
=========


.. _security.deny_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _security.deny_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _security.deny_anyfrontenduser:

anyFrontendUser
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, allows any FrontendUser unless other arguments disallows each specific FrontendUser

.. _security.deny_anyfrontendusergroup:

anyFrontendUserGroup
--------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, allows any FrontendUserGroup unless other arguments disallows each specific FrontendUser

.. _security.deny_frontenduser:

frontendUser
------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FrontendUser to allow/deny

.. _security.deny_frontendusers:

frontendUsers
-------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FrontendUsers ObjectStorage to allow/deny

.. _security.deny_frontendusergroup:

frontendUserGroup
-----------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FrontendUserGroup to allow/deny

.. _security.deny_frontendusergroups:

frontendUserGroups
------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FrontendUserGroups ObjectStorage to allow/deny

.. _security.deny_anybackenduser:

anyBackendUser
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, allows any backend user unless other arguments disallows each specific backend user

.. _security.deny_backenduser:

backendUser
-----------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of a backend user to allow/deny

.. _security.deny_backendusers:

backendUsers
------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The backend users list to allow/deny. If string, CSV of uids assumed, if array, array of uids assumed

.. _security.deny_backendusergroup:

backendUserGroup
----------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of the backend user group to allow/deny

.. _security.deny_backendusergroups:

backendUserGroups
-----------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The backend user groups list to allow/deny. If string, CSV of uids is assumed, if array, array of uids is assumed

.. _security.deny_admin:

admin
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, a backend user which is also an admin is required

.. _security.deny_evaluationtype:

evaluationType
--------------

:aspect:`DataType`
   string

:aspect:`Default`
   'AND'

:aspect:`Required`
   false
:aspect:`Description`
   Specify AND or OR (case sensitive) to determine how arguments must be processed. Default is AND, requiring all arguments to be satisfied if used
