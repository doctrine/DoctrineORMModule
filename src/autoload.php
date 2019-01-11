<?php

// Provide compatibility with doctrine/migrations v1 and v2.
// In the whole library we are using doctrine/migrations v2 class names
// and here we are creating aliases in case v1 is used.

$classMap = [
    Doctrine\DBAL\Migrations\Configuration\Configuration::class
        => Doctrine\Migrations\Configuration\Configuration::class,
    Doctrine\DBAL\Migrations\OutputWriter::class
        => Doctrine\Migrations\OutputWriter::class,
    Doctrine\DBAL\Migrations\AbstractMigration::class
        => Doctrine\Migrations\AbstractMigration::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand::class
        => Doctrine\Migrations\Tools\Console\Command\AbstractCommand::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand::class
        => Doctrine\Migrations\Tools\Console\Command\DiffCommand::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand::class
        => Doctrine\Migrations\Tools\Console\Command\ExecuteCommand::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand::class
        => Doctrine\Migrations\Tools\Console\Command\GenerateCommand::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand::class
        => Doctrine\Migrations\Tools\Console\Command\LatestCommand::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand::class
        => Doctrine\Migrations\Tools\Console\Command\MigrateCommand::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand::class
        => Doctrine\Migrations\Tools\Console\Command\StatusCommand::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\UpToDateCommand::class
        => Doctrine\Migrations\Tools\Console\Command\UpToDateCommand::class,
    Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand::class
        => Doctrine\Migrations\Tools\Console\Command\VersionCommand::class,
];

foreach ($classMap as $legacy => $newClass) {
    if (! class_exists($newClass) && class_exists($legacy)) {
        class_alias($legacy, $newClass);
    }
}
