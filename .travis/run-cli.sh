./vendor/DoctrineModule/bin/doctrine dbal:import ./vendor/DoctrineORMModule/.travis/dummy-import.sql
./vendor/DoctrineModule/bin/doctrine dbal:run-sql "SELECT 1"
./vendor/DoctrineModule/bin/doctrine orm:clear-cache:metadata
./vendor/DoctrineModule/bin/doctrine orm:clear-cache:query
./vendor/DoctrineModule/bin/doctrine orm:clear-cache:result
./vendor/DoctrineModule/bin/doctrine orm:clear-cache:query
./vendor/DoctrineModule/bin/doctrine orm:ensure-production-settings
./vendor/DoctrineModule/bin/doctrine orm:generate-proxies
./vendor/DoctrineModule/bin/doctrine orm:info
./vendor/DoctrineModule/bin/doctrine orm:run-dql "SELECT COUNT(a) FROM TravisTest\TestEntity a"
./vendor/DoctrineModule/bin/doctrine orm:schema-tool:create
./vendor/DoctrineModule/bin/doctrine orm:schema-tool:update
./vendor/DoctrineModule/bin/doctrine orm:schema-tool:validate-schema
./vendor/DoctrineModule/bin/doctrine orm:schema-tool:drop
./vendor/DoctrineModule/bin/doctrine migrations:generate --configuration=vendor/DoctrineORMModule/.travis/migrations-config.xml
./vendor/DoctrineModule/bin/doctrine migrations:diff --configuration=vendor/DoctrineORMModule/.travis/migrations-config.xml
