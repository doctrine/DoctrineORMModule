./vendor/bin/doctrine-module migrations:generate
./vendor/bin/doctrine-module migrations:diff
./vendor/bin/doctrine-module migrations:execute 20120714005702 -n
./vendor/bin/doctrine-module migrations:migrate --no-interaction
