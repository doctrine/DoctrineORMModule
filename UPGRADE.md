# 0.3.1
Version `0.3.1` integrated CLI with composer's CLI scripts registration method. To do so without conflicting with
Doctrine ORM's CLI, you now have to access it either via `./vendor/bin/doctrine-module` or
`./vendor/doctrine/DoctrineModule/bin/doctrine-module`.

# 0.3.0
After version `0.2.1`, submodules have been dropped as they were too heavy and complex to manage. If you now want to use
this module, please use composer, as described in
[README.md](http://www.github.com/doctrine/DoctrineORMModule/tree/master/README.md)