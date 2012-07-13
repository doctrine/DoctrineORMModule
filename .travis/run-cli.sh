./vendor/bin/doctrine-module dbal:import ./vendor/doctrine/doctrine-orm-module/.travis/dummy-import.sql
./vendor/bin/doctrine-module dbal:run-sql "SELECT 1"
./vendor/bin/doctrine-module orm:clear-cache:metadata
./vendor/bin/doctrine-module orm:clear-cache:query
./vendor/bin/doctrine-module orm:clear-cache:result
./vendor/bin/doctrine-module orm:clear-cache:query
./vendor/bin/doctrine-module orm:generate-proxies
./vendor/bin/doctrine-module orm:ensure-production-settings
./vendor/bin/doctrine-module orm:info
./vendor/bin/doctrine-module orm:schema-tool:create
./vendor/bin/doctrine-module orm:schema-tool:update
./vendor/bin/doctrine-module orm:validate-schema
./vendor/bin/doctrine-module orm:run-dql "SELECT COUNT(a) FROM DoctrineORMModuleTest\Assets\Entity\Test a"
./vendor/bin/doctrine-module orm:schema-tool:drop
./vendor/bin/doctrine-module migrations:generate --configuration=vendor/doctrine/doctrine-orm-module/.travis/migrations-config.xml
./vendor/bin/doctrine-module migrations:diff --configuration=vendor/doctrine/doctrine-orm-module/.travis/migrations-config.xml
./vendor/bin/doctrine-module migrations:execute 20120714005702 -n --configuration=vendor/doctrine/doctrine-orm-module/.travis/migrations-execute-config.xml