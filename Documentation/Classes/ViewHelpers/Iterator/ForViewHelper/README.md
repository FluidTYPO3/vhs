# Overview

The ``v:iterator.for`` view helper acts like a normal [for loop](http://www.php.net/manual/en/control-structures.for.php).
You can define a [starting number](Arguments/from.md), [end number](Arguments/to.md),
and the [stepping number](Arguments/step.md) added on each iteration.

## Suggested use

Say you have 6 images of cute cats, named ``cat1.jpg`` to ``cat6.jpg``, and you want to display them:

    <img src="cat1.jpg" alt="Cat">
    <img src="cat2.jpg" alt="Cat">
    <img src="cat3.jpg" alt="Cat">
    <img src="cat4.jpg" alt="Cat">
    <img src="cat5.jpg" alt="Cat">
    <img src="cat6.jpg" alt="Cat">

That sure is a lot of typing. With the ``v:iterator.for`` view helper you can just do:

    <v:iterator.for from="1" to="6" iteration="i">
        <img src="cat{i.index}.jpg" alt="Cat">
    </v:iterator.for>

Thats nice. Notice the [iteration](Arguments/iteration.md) argument.
This provides you with a local template variable (in this case ``i``) that you can use
to access current information about your iteration.

## Another example

Say you want to display all odd numbers up to 1000. That would be a lot of typing, but with this view helper you can just do:

    <v:iterator.for from="1" to="1000" step="2" iteration="i">
        {i.index}
    </v:iterator.for>

This is where the [step](Arguments/step.md) argument steps in (haha). It will increase (or decrease) your
[starting number](Arguments/from.md) until it reaches your [end number](Arguments/to.md) by adding its value on each iteration.

```warning
Please be aware that a [RuntimeException](http://www.php.net/manual/en/class.runtimeexception.php) is thrown if your
[stepping number](Arguments/step.md) is illegal. That means you should not set it to 0 (obviously). If your
[end number](Arguments/to.md) is lower than your [starting number](Arguments/from.md), your [stepping number](Arguments/step.md)
must be negative.
```
