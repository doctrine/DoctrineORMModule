#!/bin/bash -eux

mysql -h 127.0.0.1 -u root --password='' database < ci/dummy-import.sql
./vendor/bin/doctrine-module dbal:run-sql "SELECT 1"
./vendor/bin/doctrine-module orm:clear-cache:metadata
./vendor/bin/doctrine-module orm:clear-cache:query
./vendor/bin/doctrine-module orm:clear-cache:result
./vendor/bin/doctrine-module orm:generate-proxies
./vendor/bin/doctrine-module orm:ensure-production-settings
./vendor/bin/doctrine-module orm:info
./vendor/bin/doctrine-module orm:schema-tool:create
./vendor/bin/doctrine-module orm:schema-tool:update
./vendor/bin/doctrine-module orm:validate-schema
./vendor/bin/doctrine-module dbal:run-sql "SELECT COUNT(a.id) FROM entity a"
./vendor/bin/doctrine-module orm:run-dql "SELECT COUNT(a) FROM DoctrineORMModule\Ci\Entity\Entity a"
./vendor/bin/doctrine-module orm:schema-tool:drop --dump-sql
./vendor/bin/doctrine-module orm:schema-tool:drop --force
