::

      _, __,  _, _ __, _  _, _, _
     / \ |_) (_  | | \ | /_\ |\ |
     \ / |_) , ) | |_/ | | | | \|
      ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
::

Description
===========

Obsidian offers workspace dashboard, for projects management purposes and
hosting of specific applications.

This is a community technical preview.

It uses PHP, HTML, CSS and JS, MySQL, and a document storage engine.
For that, MongoDB is recommended, but anything is good (files, MySQL,
or SQLite3).

License
-------
The code is dual licensed under BSD license and Apache License 2.0,
the UI front-end under Apache License 2.0.


Unit testing
=============

The tests uses PHPUnit. If you don't have it installed, see
http://phpunit.de/getting-started.html

To run the tests:

    cd tests
    make test


Credits for third-party software components
===========================================

Core libraries
--------------

* Obsidian is based on Keruald/Xen, by Sébastien Santoro aka Dereckson, licensed
  licensed under BSD license. It offers a lightweight MVC PHP engine, with l10n
  and templates handled by Smarty.

* Some cache and security features are forked from Zed, same author and license.

* Smarty is licensed under LGPL.
    
UI
--

* The front-end UI is based on SB Admin v2, part of Start Bootstrap, a project
  maintained by Iron Summit Media Strategies to offerBootstrap-based templates.
  This template is available under Apache License 2.0 license.

* Bootstrap is a CSS responsive framework, licensed under Apache License 2.0.
