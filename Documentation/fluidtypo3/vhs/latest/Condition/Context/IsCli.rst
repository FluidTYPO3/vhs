.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-context-iscli:

=======================
condition.context.isCli
=======================


Condition: Is context CLI?
==========================

A condition ViewHelper which renders the `then` child if
current context being rendered is CLI.

Examples
========

::

    <!-- simple usage, content becomes then-child -->
    <v:condition.context.isCli>
        Hooray for CLI contexts!
    </v:condition.context.isCli>
    <!-- extended use combined with f:then and f:else -->
    <v:condition.context.isCli>
        <f:then>
           Hooray for CLI contexts!
        </f:then>
        <f:else>
           Maybe BE, maybe FE.
        </f:else>
    </v:condition.context.isCli>

Arguments
=========


.. _condition.context.iscli_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.context.iscli_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.
