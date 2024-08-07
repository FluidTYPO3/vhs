:navigation-title: once.instance
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-once-instance:

==============================================
once.instance ViewHelper `<vhs:once.instance>`
==============================================


Once: Instance

Displays nested content or "then" child once per instance
of the content element or plugin being rendered, as identified
by the contentObject UID (or globally if no contentObject
is associated).

"Once"-style ViewHelpers are purposed to only display their
nested content once per XYZ, where the XYZ depends on the
specific type of ViewHelper (session, cookie etc).

In addition the ViewHelper is a ConditionViewHelper, which
means you can utilize the f:then and f:else child nodes as
well as the "then" and "else" arguments.


.. _fluidtypo3-vhs-once-instance_arguments:

Arguments
=========


.. _once.instance_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _once.instance_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _once.instance_identifier:

identifier
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Identity of this condition - if used in other places, the condition applies to the same identity in the storage (i.e. cookie name or session key)

.. _once.instance_locktodomain:

lockToDomain
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, locks this condition to a specific domain, i.e. the storage of $identity is associated with a domain. If same identity is also used without domain lock, it matches any domain locked condition

.. _once.instance_ttl:

ttl
---

:aspect:`DataType`
   integer

:aspect:`Default`
   86400

:aspect:`Required`
   false
:aspect:`Description`
   Time-to-live for skip registration, number of seconds. After this expires the registration is unset
