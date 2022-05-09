.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-context-isfrontend:

============================
condition.context.isFrontend
============================


Condition: Is context Frontend?
===============================

A condition ViewHelper which renders the `then` child if
current context being rendered is FE.

Examples
========

::

    <!-- simple usage, content becomes then-child -->
    <v:condition.context.isFrontend>
        Hooray for BE contexts!
    </v:condition.context.isFrontend>
    <!-- extended use combined with f:then and f:else -->
    <v:condition.context.isFrontend>
        <f:then>
           Hooray for BE contexts!
        </f:then>
        <f:else>
           Maybe BE, maybe CLI.
        </f:else>
    </v:condition.context.isFrontend>

Arguments
=========


.. _condition.context.isfrontend_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.context.isfrontend_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.
