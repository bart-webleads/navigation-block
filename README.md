# Module: Navigation Block

Navigation Block is a module for Fork CMS.
This module can be used for adding custom navigation anywhere on your page.

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

You can add a "fixed" footer menu using the parsewidget modifier. You can find the category ID in the category overview (modules > Navigation Block > Category)

### Example
    {$var|parsewidget:"navigation_block":"detail":"1"}
    {$var|parsewidget:"navigation_block":"detail":"2"}
    {$var|parsewidget:"navigation_block":"detail":"3"}

## Improvements

* Need to add other [languages than Dutch and English] (http://www.fork-cms.com/community/documentation/detail/module-guide/translations-or-locale)
* Add caching

## Support

* E-mail: bart@webleads.nl
