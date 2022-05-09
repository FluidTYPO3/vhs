.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-render-ascii:

============
render.ascii
============


Render: ASCII Character
=======================

Renders a single character identified by its charset number.

For example: `<v:render.character ascii="10" /> renders a UNIX linebreak
as does {v:render.character(ascii: 10)}. Can be used in combination with
`v:iterator.loop` to render sequences or repeat the same character:

::

    {v:render.ascii(ascii: 10) -> v:iterator.loop(count: 5)}

And naturally you can feed any integer variable or ViewHelper return value
into the `ascii` parameter throught `renderChildren` to allow chaining:

::

    {variableWithAsciiInteger -> v:render.ascii()}

And arrays are also supported - they will produce a string of characters
from each number in the array:

::

    {v:render.ascii(ascii: {0: 13, 1: 10})}

Will produce a Windows line break, \r\n.

Arguments
=========


.. _render.ascii_ascii:

ascii
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   ASCII character to render
