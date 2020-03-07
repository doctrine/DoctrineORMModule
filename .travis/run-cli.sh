travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module dbal:import .travis/dummy-import.sql
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module dbal:run-sql "SELECT 1"
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:clear-cache:metadata
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:clear-cache:query
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:clear-cache:result
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:generate-proxies
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:ensure-production-settings
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:info
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:schema-tool:create
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:schema-tool:update
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:validate-schema
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module dbal:run-sql "SELECT COUNT(a.id) FROM entity a"
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:run-dql "SELECT COUNT(a) FROM DoctrineORMModule\Travis\Entity\Entity a"
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:schema-tool:drop --dump-sql
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module orm:schema-tool:drop --force
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module migrations:generate
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module migrations:diff
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module migrations:execute 20120714005702 -n
travis_retry docker-compose run --rm php ./vendor/bin/doctrine-module migrations:migrate
