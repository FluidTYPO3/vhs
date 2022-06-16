.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-once-cookie:

===========
once.cookie
===========


Once: Cookie

Displays nested content or "then" child once, then sets a
cookie with $ttl, optionally locked to domain name, which
makes the condition return FALSE as long as the cookie exists.

"Once"-style ViewHelpers are purposed to only display their
nested content once per XYZ, where the XYZ depends on the
specific type of ViewHelper (session, cookie etc).

In addition the ViewHelper is a ConditionViewHelper, which
means you can utilize the f:then and f:else child nodes as
well as the "then" and "else" arguments.

Arguments
=========


.. _once.cookie_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _once.cookie_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _once.cookie_identifier:

identifier
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Identity of this condition - if used in other places, the condition applies to the same identity in the storage (i.e. cookie name or session key)

.. _once.cookie_locktodomain:

lockToDomain
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, locks this condition to a specific domain, i.e. the storage of $identity is associated with a domain. If same identity is also used without domain lock, it matches any domain locked condition

.. _once.cookie_ttl:

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
