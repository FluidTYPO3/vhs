.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-format-sanitizestring:

=====================
format.sanitizeString
=====================


URL text segment sanitizer. Sanitizes the content into a
valid URL segment value which is usable in an URL without
further processing. For example, the text "I am Mr. Brown,
how are you?" becomes "i-am-mr-brown-how-are-you". Special
characters like diacritics or umlauts are transliterated.
The built-in character map can be overriden or extended by
providing an associative array of custom mappings.

Also useful when creating anchor link names, for example
for news entries in your custom EXT:news list template, in
which case each news item's title would become an anchor:

<a name="{newsItem.title -> v:format.url.sanitizeString()}"></a>

And links would look much like the detail view links:

/news/#this-is-a-newsitem-title

When used with list views it has the added benefit of not
breaking if the item referenced is removed, it can be read
by Javascript (for example to dynamically expand the news
item being referenced). The sanitized urls are also ideal
to use for AJAX based detail views - and in almot all cases
the sanitized string will be 100% identical to the one used
by Realurl when translating using table lookups.

Arguments
=========


.. _format.sanitizestring_string:

string
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The string to sanitize.

.. _format.sanitizestring_custommap:

customMap
---------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Associative array of additional characters to replace or use to override built-in mappings.
