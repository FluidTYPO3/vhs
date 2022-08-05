.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-page-header-title:

=================
page.header.title
=================


ViewHelper used to override page title
======================================

This ViewHelper uses the TYPO3 PageRenderer to set the
page title - with everything this implies regarding
support for TypoScript settings.

Specifically you should note the setting `config.noPageTitle`
which must be set to either 1 (one) in case no other source
defines the page title (it's likely that at least one does),
or 2 (two) to indicate that the TS-controlled page title
must be disabled. A value of 2 (two) ensures that the title
used in this ViewHelper will be used in the rendered page.

If you use the ViewHelper in a plugin it has to be USER
not USER_INT, what means it has to be cached!

Why can I not forcibly override the title?
------------------------------------------

This has been opted out with full intention. The reasoning
behind not allowing a Fluid template to forcibly override the
page title that may be set through TypoScript is that many
other extensions (mainly SEO-focused ones) will be setting
and manipulating the page title - and if overridden in a
template file using a ViewHelper, it would be almost impossible
to detect unless you already know exactly where to look.
Enforcing use of the core behavior is the only way to ensure
that this ViewHelper can coexist with other extensions in
a fully controllable way.

Arguments
=========


.. _page.header.title_title:

title
-----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Title tag content

.. _page.header.title_whitespacestring:

whitespaceString
----------------

:aspect:`DataType`
   string

:aspect:`Default`
   ' '

:aspect:`Required`
   false
:aspect:`Description`
   String used to replace groups of white space characters, one replacement inserted per group

.. _page.header.title_setindexeddoctitle:

setIndexedDocTitle
------------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Set indexed doc title to title
