./vendor/doctrine/DoctrineModule/bin/doctrine dbal:import ./vendor/doctrine/DoctrineORMModule/.travis/dummy-import.sql
./vendor/doctrine/DoctrineModule/bin/doctrine dbal:run-sql "SELECT 1"
./vendor/doctrine/DoctrineModule/bin/doctrine orm:clear-cache:metadata
./vendor/doctrine/DoctrineModule/bin/doctrine orm:clear-cache:query
./vendor/doctrine/DoctrineModule/bin/doctrine orm:clear-cache:result
./vendor/doctrine/DoctrineModule/bin/doctrine orm:clear-cache:query
./vendor/doctrine/DoctrineModule/bin/doctrine orm:generate-proxies
./vendor/doctrine/DoctrineModule/bin/doctrine orm:ensure-production-settings
./vendor/doctrine/DoctrineModule/bin/doctrine orm:info
./vendor/doctrine/DoctrineModule/bin/doctrine orm:schema-tool:create
./vendor/doctrine/DoctrineModule/bin/doctrine orm:schema-tool:update
./vendor/doctrine/DoctrineModule/bin/doctrine orm:validate-schema
./vendor/doctrine/DoctrineModule/bin/doctrine orm:run-dql "SELECT COUNT(a) FROM DoctrineORMModuleTest\Assets\Entity\Test a"
./vendor/doctrine/DoctrineModule/bin/doctrine orm:schema-tool:drop
./vendor/doctrine/DoctrineModule/bin/doctrine migrations:generate --configuration=vendor/doctrine/DoctrineORMModule/.travis/migrations-config.xml
./vendor/doctrine/DoctrineModule/bin/doctrine migrations:diff --configuration=vendor/doctrine/DoctrineORMModule/.travis/migrations-config.xml