# Module: Navigation Block

Navigation Block is a module for [Fork CMS](http://www.fork-cms.com).
This module can be used for adding custom navigation anywhere on your page.

## Versions

* Version 2.x.x will work on ForkCMS 3.7.x
* Version 1.x.x will work on ForkCMS 3.6.x

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
* Custom category templates. You can add a custom template per category. For example footer items are shown "standard" (list of links). While some large buttons in the center of the page can use a different template with a complete other look.
* You can add a "fixed" menu (handy for footer menus) using the parsewidget modifier and the category:alias parameter. (see example)

## Example
    {$var|parsewidget:"NavigationBlock":"Detail":"footleft"}
    {$var|parsewidget:"NavigationBlock":"Detail":"footright"}

## Improvements

* Need to add other [languages than Dutch and English] (http://www.fork-cms.com/community/documentation/detail/module-guide/translations-or-locale)
* Maybe add some caching?

## Support

* E-mail: bart@webleads.nl
