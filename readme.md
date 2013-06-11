Drop It
======
![Build Status](https://magnum-ci.com/status/834724296817537285abf7da3a3c62e9.png)

## Description
TL;DR: Drag and drop layout management framework for WordPress. Comes with a set of widgets aka "drops".

## Extended Description
The goal was to build a powerful and extensible, yet easy to use layout management plugin.

Essentially, the plugin gives users an ability to create grid of a certain size (Drop Zone) and populate it with widgets (Drops) that might represent different content (static html, single post, custom query, whatever data you want to present). You can shuffle drops around the grid as you want.

## Implementation Details
Each drop is represented by Drop_It_Drop child class. Think of it as an MVC wannabe. Backend UI utilizes [Backbone.js](http://backbonejs.org/) (min 1.0). [Underscore.js](http://underscorejs.org) for templating, frontend utilizes [Twig](http://twig.sensiolabs.org/) for templating. I know, I know, PHP is a templating engine itself. The idea behind using templating engine is that users will be able to create multiple templates for each registered drop right from the admin, and without breaking anything (yet to be implemented).

Each drop instance has a set of basic values:
* type (one of registered types)
* colspan ()
* column (in the grid)
* row (in the grid)

## Disclaimer
Current implementation is ~~half-assed~~ under heavy development.

## Feedback
Pull requests, bug reports, and feature requests are welcome.

## Submodules Init and Update

1. Pull as usual
2. Do `git submodule -q foreach git pull -q origin master` to update submodules
3. ...
4. Profit