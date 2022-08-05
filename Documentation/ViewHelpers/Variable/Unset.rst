.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-variable-unset:

==============
variable.unset
==============


Variable: Unset
===============

Quite simply, removes a currently available variable
from the TemplateVariableContainer:

::

    <!-- Data: {person: {name: 'Elvis', nick: 'King'}} -->
    I'm {person.name}. Call me "{person.nick}". A ding-dang doo!
    <v:variable.unset name="person" />
    <f:if condition="{person}">
        <f:else>
            You saw this coming...
            <em>Elvis has left the building</em>
        </f:else>
    </f:if>

At the time of writing this, `v:variable.unset` is not able
to remove members of for example arrays:

::

    <!-- DOES NOT WORK! -->
    <v:variable.unset name="myObject.propertyName" />

Arguments
=========


.. _variable.unset_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of variable in variable container
