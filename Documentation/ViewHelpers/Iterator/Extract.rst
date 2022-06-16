.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-extract:

================
iterator.extract
================


Iterator / Extract VieWHelper
=============================

Loop through the iterator and extract a key, optionally join the
results if more than one value is found.

Extract values from an array by key
-----------------------------------

The extbase version of indexed_search returns an array of the
previous search, which cannot easily be shown in the input field
of the result page. This can be solved.

Input from extbase version of indexed_search">
----------------------------------------------

::

    [
        0 => [
            'sword' => 'firstWord',
            'oper' => 'AND'
        ],
        1 => [
            'sword' => 'secondWord',
            'oper' => 'AND'
        ],
        3 => [
            'sword' => 'thirdWord',
            'oper' => 'AND'
        ]
    ]

Show the previous search words in the search form of the
result page:

Example
-------

::

    <f:form.textfield name="search[sword]"
        value="{v:iterator.extract(key:'sword', content: searchWords) -> v:iterator.implode(glue: ' ')}"
        class="tx-indexedsearch-searchbox-sword" />

Get the names of several users
------------------------------

Provided we have a bunch of FrontendUsers and we need to show
their firstname combined into a string:

::

    <h2>Welcome
    <v:iterator.implode glue=", "><v:iterator.extract key="firstname" content="frontendUsers" /></v:iterator.implode>
    <!-- alternative: -->
    {frontendUsers -> v:iterator.extract(key: 'firstname') -> v:iterator.implode(glue: ', ')}
    </h2>

Output
------

::

    <h2>Welcome Peter, Paul, Marry</h2>

Complex example
---------------

::

    {anArray->v:iterator.extract(path: 'childProperty.secondNestedChildObject')
        -> v:iterator.sort(direction: 'DESC', sortBy: 'propertyOnSecondChild')
        -> v:iterator.slice(length: 10)->v:iterator.extract(key: 'uid')}

Single return value
-------------------

Outputs the "uid" value of the first record in variable $someRecords without caring if there are more than
one records. Always extracts the first value and then stops. Equivalent of changing -> v:iterator.first().

::

    {someRecords -> v:iterator.extract(key: 'uid', single: TRUE)}

Arguments
=========


.. _iterator.extract_content:

content
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The array or Iterator that contains either the value or arrays of values

.. _iterator.extract_key:

key
---

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The name of the key from which you wish to extract the value

.. _iterator.extract_recursive:

recursive
---------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, attempts to extract the key from deep nested arrays

.. _iterator.extract_single:

single
------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, returns only one value - always the first one - instead of an array of values
