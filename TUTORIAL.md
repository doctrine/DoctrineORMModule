# Moving from Doctrine Quickstart to Zend Framework 2

This tutorial is meant to help you migrate from an existing Doctrine 2 project to a 
working ZF2 Doctrine project. This basically catalogues my adventures in trying to
move from a working version of the Doctrine quickstart which can be found here:
http://docs.doctrine-project.org/en/latest/tutorials/getting-started.html
to a working version of the same tutorial in a Zend Framework 2 skeleton application.

Thanks to Marco for his patience, this is aimed to answer the many questions I 
pestered him with in IRC to avoid others having to ask them!

I followed the "Getting Started" tutorial and used the annotations for my mapping
information, you may need to tweak this if you used xml or yaml.

Prerequisits
------------
* Completed version of the Doctrine "Getting Started" tutorial (Bug Tracker)
* Empty working copy of the Zend Framework 2 Skeleton Application
https://github.com/zendframework/ZendSkeletonApplication

Installing
----------
Follow the installation instructions for DoctrineORM Module at
https://github.com/doctrine/DoctrineORMModule
