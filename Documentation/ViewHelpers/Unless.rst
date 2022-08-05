.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-unless:

======
unless
======


Unless
======

The opposite of `f:if` and only supporting negative matching.
Related to `v:or` but allows more complex conditions.

Is the same as writing:

::

    <f:if condition="{theThingToCheck}">
        <f:else>
            The thing that gets done
        </f:else>
    </f:if>

Except without the `f:else`.

Example, tag mode
-----------------

::

    <v:unless condition="{somethingRequired}">
        Warning! Something required was not present.
    </v:unless>

Example, inline mode illustrating `v:or` likeness
-------------------------------------------------

::

    {defaultText -> v:unless(condition: originalText)}
        // which is much the same as...
    {originalText -> v:or(alternative: defaultText}
        // ...but the "unless" counterpart supports anything as
        // condition instead of only checking "is content empty?"

Arguments
=========


.. _unless_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _unless_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _unless_condition:

condition
---------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Condition which must be true, or then is rendered
