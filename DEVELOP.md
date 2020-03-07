Development
===========

Unit tests rely on a mysql installation so all unit tests are ran through 
docker.  To run all unit tests execute:

```
docker-compose run --rm php composer test
```

Running unit tests with code coverage requires you build the docker
composer with XDEBUG=1

```
docker-compose build --build-arg XDEBUG=1
```

To change docker to a different php version

```
docker-compose build --build-arg PHP_VERSION=7.3
```

then run the unit tests as 

```
docker-compose run --rm php composer test-coverage
```

Run phpcs as 
```
docker-compose run --rm php composer cs-check src test config
```


Console Commands
----------------

To run functional tests on console commands as found in `.travis/run-cli.sh` you must first copy `.travis/config/application.config.php` to `config/application.config.php`
