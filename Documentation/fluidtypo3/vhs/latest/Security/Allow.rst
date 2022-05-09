.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-security-allow:

==============
security.allow
==============


Security: Allow
===============

Allows access to the child content based on given arguments.
The ViewHelper is a condition based ViewHelper which means it
supports the `f:then` and `f:else` child nodes - you can use
this behaviour to invert the access (i.e. use f:else in a check
if a frontend user is logged in, if you want to hide content
from authenticated users):

::

    <v:security.allow anyFrontendUser="TRUE">
        <f:then><!-- protected information displayed --></f:then>
        <f:else><!-- link to login form displayed --></f:else>
    </v:security.allow>

Is the mirror opposite of `v:security.deny`.

Arguments
=========


.. _security.allow_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _security.allow_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _security.allow_anyfrontenduser:

anyFrontendUser
---------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, allows any FrontendUser unless other arguments disallows each specific FrontendUser

.. _security.allow_anyfrontendusergroup:

anyFrontendUserGroup
--------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, allows any FrontendUserGroup unless other arguments disallows each specific FrontendUser

.. _security.allow_frontenduser:

frontendUser
------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FrontendUser to allow/deny

.. _security.allow_frontendusers:

frontendUsers
-------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FrontendUsers ObjectStorage to allow/deny

.. _security.allow_frontendusergroup:

frontendUserGroup
-----------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FrontendUserGroup to allow/deny

.. _security.allow_frontendusergroups:

frontendUserGroups
------------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The FrontendUserGroups ObjectStorage to allow/deny

.. _security.allow_anybackenduser:

anyBackendUser
--------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, allows any backend user unless other arguments disallows each specific backend user

.. _security.allow_backenduser:

backendUser
-----------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of a backend user to allow/deny

.. _security.allow_backendusers:

backendUsers
------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The backend users list to allow/deny. If string, CSV of uids assumed, if array, array of uids assumed

.. _security.allow_backendusergroup:

backendUserGroup
----------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The uid of the backend user group to allow/deny

.. _security.allow_backendusergroups:

backendUserGroups
-----------------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The backend user groups list to allow/deny. If string, CSV of uids is assumed, if array, array of uids is assumed

.. _security.allow_admin:

admin
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, a backend user which is also an admin is required

.. _security.allow_evaluationtype:

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
