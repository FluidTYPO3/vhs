.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-variable-set:

============
variable.set
============


Variable: Set
=============

Sets a single variable in the TemplateVariableContainer
scope. The variable then becomes accessible as {var}.

Combines well with `v:variable.get` to set shorter variable
names referencing dynamic variables, such as:

::

    <v:variable.set name="myObject" value="{v:variable.get(name: 'arrayVariable.{offset}')}" />
    <!-- If {index} == 4 then {myObject} is now == {arrayVariable.4} -->
    {myObject.name} <!-- corresponds to {arrayVariable.4.name} -->

Note that `{arrayVariable.{offset}.name}` is not possible
due to the way Fluid parses nodes; the above piece of
code would try reading `arrayVariable.{offset}.name`
as a variable actually called "arrayVariable.{offset}.name"
rather than the correct `arrayVariable[offset][name]`.

In many ways this ViewHelper works like `f:alias`
with one exception: in `f:alias` the variable only
becomes accessible in the tag content, whereas `v:variable.set`
inserts the variable in the template and leaves it there
(it "leaks" the variable).

If $name contains a dot, VHS will attempt to load the object
stored under the named used as the first segment part and
set the value at the remaining path. E.g.
`{value -> v:variable.set(name: 'object.property.subProperty')}`
would attempt to load `{object}` first, then set
`property.subProperty` on that object/array using
ObjectAccess::setPropertyPath(). If `{object}` is not
an object or an array, the variable will not be set. Please
note: Extbase does not currently support setting variables
deeper than two levels, meaning a `name` of fx `foo.bar.baz`
will be ignored. To set values deeper than two levels you
must first extract the second-level object then set the
value on that object.

Using as `{value -> v:variable.set(name: 'myVar')}` makes `{myVar}` contain
`{value}`.

Arguments
=========


.. _variable.set_value:

value
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to set

.. _variable.set_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of variable to assign
