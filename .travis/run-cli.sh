docker-compose run --rm php ./vendor/bin/doctrine-module dbal:import .travis/dummy-import.sql
docker-compose run --rm php ./vendor/bin/doctrine-module dbal:run-sql "SELECT 1"
docker-compose run --rm php ./vendor/bin/doctrine-module orm:clear-cache:metadata
docker-compose run --rm php ./vendor/bin/doctrine-module orm:clear-cache:query
docker-compose run --rm php ./vendor/bin/doctrine-module orm:clear-cache:result
docker-compose run --rm php ./vendor/bin/doctrine-module orm:generate-proxies
docker-compose run --rm php ./vendor/bin/doctrine-module orm:ensure-production-settings
docker-compose run --rm php ./vendor/bin/doctrine-module orm:info
docker-compose run --rm php ./vendor/bin/doctrine-module orm:schema-tool:create
docker-compose run --rm php ./vendor/bin/doctrine-module orm:schema-tool:update
docker-compose run --rm php ./vendor/bin/doctrine-module orm:validate-schema
docker-compose run --rm php ./vendor/bin/doctrine-module dbal:run-sql "SELECT COUNT(a.id) FROM entity a"
docker-compose run --rm php ./vendor/bin/doctrine-module orm:run-dql "SELECT COUNT(a) FROM DoctrineORMModule\Travis\Entity\Entity a"
docker-compose run --rm php ./vendor/bin/doctrine-module orm:schema-tool:drop --dump-sql
docker-compose run --rm php ./vendor/bin/doctrine-module orm:schema-tool:drop --force
docker-compose run --rm php ./vendor/bin/doctrine-module migrations:generate
docker-compose run --rm php ./vendor/bin/doctrine-module migrations:diff
docker-compose run --rm php ./vendor/bin/doctrine-module migrations:execute 20120714005702 -n
docker-compose run --rm php ./vendor/bin/doctrine-module migrations:migrate --no-interaction
