.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-context-isbackend:

===========================
condition.context.isBackend
===========================


Condition: Is context Backend?
==============================

A condition ViewHelper which renders the `then` child if
current context being rendered is BE.

Examples
========

::

    <!-- simple usage, content becomes then-child -->
    <v:condition.context.isBackend>
        Hooray for BE contexts!
    </v:condition.context.isBackend>
    <!-- extended use combined with f:then and f:else -->
    <v:condition.context.isBackend>
        <f:then>
           Hooray for BE contexts!
        </f:then>
        <f:else>
           Maybe FE, maybe CLI.
        </f:else>
    </v:condition.context.isBackend>

Arguments
=========


.. _condition.context.isbackend_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.context.isbackend_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.
