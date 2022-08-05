.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-once-session:

============
once.session
============


Once: Session

Displays nested content or "then" child once per session.

"Once"-style ViewHelpers are purposed to only display their
nested content once per XYZ, where the XYZ depends on the
specific type of ViewHelper (session, cookie etc).

In addition the ViewHelper is a ConditionViewHelper, which
means you can utilize the f:then and f:else child nodes as
well as the "then" and "else" arguments.

Arguments
=========


.. _once.session_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _once.session_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _once.session_identifier:

identifier
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Identity of this condition - if used in other places, the condition applies to the same identity in the storage (i.e. cookie name or session key)

.. _once.session_locktodomain:

lockToDomain
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, locks this condition to a specific domain, i.e. the storage of $identity is associated with a domain. If same identity is also used without domain lock, it matches any domain locked condition

.. _once.session_ttl:

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
