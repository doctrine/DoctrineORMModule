./vendor/bin/doctrine dbal:import ./vendor/doctrine/DoctrineORMModule/.travis/dummy-import.sql
./vendor/bin/doctrine dbal:run-sql "SELECT 1"
./vendor/bin/doctrine orm:clear-cache:metadata
./vendor/bin/doctrine orm:clear-cache:query
./vendor/bin/doctrine orm:clear-cache:result
./vendor/bin/doctrine orm:clear-cache:query
./vendor/bin/doctrine orm:generate-proxies
./vendor/bin/doctrine orm:ensure-production-settings
./vendor/bin/doctrine orm:info
./vendor/bin/doctrine orm:schema-tool:create
./vendor/bin/doctrine orm:schema-tool:update
./vendor/bin/doctrine orm:validate-schema
./vendor/bin/doctrine orm:run-dql "SELECT COUNT(a) FROM DoctrineORMModuleTest\Assets\Entity\Test a"
./vendor/bin/doctrine orm:schema-tool:drop
./vendor/bin/doctrine migrations:generate --configuration=vendor/doctrine/DoctrineORMModule/.travis/migrations-config.xml
./vendor/bin/doctrine migrations:diff --configuration=vendor/doctrine/DoctrineORMModule/.travis/migrations-config.xml