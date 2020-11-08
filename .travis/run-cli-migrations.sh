./vendor/bin/doctrine-module migrations:generate
./vendor/bin/doctrine-module migrations:diff -n
./vendor/bin/doctrine-module migrations:execute --up 'TravisDoctrineMigrations\Version20120714005702' -n
./vendor/bin/doctrine-module migrations:migrate -n
./vendor/bin/doctrine-module migrations:sync-metadata-storage
./vendor/bin/doctrine-module migrations:list
./vendor/bin/doctrine-module migrations:current
./vendor/bin/doctrine-module migrations:latest
./vendor/bin/doctrine-module migrations:up-to-date
./vendor/bin/doctrine-module migrations:status --no-interaction
./vendor/bin/doctrine-module migrations:version --delete 'TravisDoctrineMigrations\Version20120714005702' -n
