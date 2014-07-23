# Module: Navigation Block

Navigation Block is a module for [Fork CMS](http://www.fork-cms.com).
This module can be used for adding custom navigation anywhere on your page.

## Versions

Version 1.x.x will work on ForkCMS 3.6.x
Version 2.x.x will work on ForkCMS 3.7.x (working on this)

## Download

Download latest release from [the release page](https://github.com/bart-lysander/navigation-block/releases).

## Installation

Visit the [Fork CMS Documentation](http://www.fork-cms.com/community/documentation/detail/getting-started/adding-modules) to learn how to install a module.

## Features

* Menus are organised using categories
* You can create your own menus that link to existing pages
* When adding a menu, you can set the recursion level
* When adding a menu, you can set a custom class name (for adding extra CSS style)
* When adding a menu, you can set a custom description (becomes the anchor title)
* Multilingual support (other languages can have other menu entries)

## Notes

You can add a "fixed" footer menu using the parsewidget modifier and the alias parameter.

### Example
    {$var|parsewidget:"navigation_block":"detail":"main"}
    {$var|parsewidget:"navigation_block":"detail":"footleft"}
    {$var|parsewidget:"navigation_block":"detail":"footright"}

## Improvements

* Need to add other [languages than Dutch and English] (http://www.fork-cms.com/community/documentation/detail/module-guide/translations-or-locale)
* Add caching

## Support

* E-mail: bart@webleads.nl
