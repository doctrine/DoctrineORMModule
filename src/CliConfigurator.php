<?php

declare(strict_types=1);

namespace DoctrineORMModule;

use Doctrine\DBAL\Tools\Console\Command\ImportCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Laminas\Stdlib\ArrayUtils;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

use function class_exists;

class CliConfigurator
{
    private string $defaultObjectManagerName = 'doctrine.entitymanager.orm_default';

    /** @var string[] */
    private array $commands = [
        'doctrine.dbal_cmd.runsql',
        'doctrine.dbal_cmd.reserved_words',
        'doctrine.orm_cmd.clear_cache_metadata',
        'doctrine.orm_cmd.clear_cache_result',
        'doctrine.orm_cmd.clear_cache_query',
        'doctrine.orm_cmd.schema_tool_create',
        'doctrine.orm_cmd.schema_tool_update',
        'doctrine.orm_cmd.schema_tool_drop',
        'doctrine.orm_cmd.ensure_production_settings',
        'doctrine.orm_cmd.convert_d1_schema',
        'doctrine.orm_cmd.generate_repositories',
        'doctrine.orm_cmd.generate_entities',
        'doctrine.orm_cmd.generate_proxies',
        'doctrine.orm_cmd.convert_mapping',
        'doctrine.orm_cmd.run_dql',
        'doctrine.orm_cmd.validate_schema',
        'doctrine.orm_cmd.info',
    ];

    /** @var string[] */
    private array $migrationCommands = [
        'doctrine.migrations_cmd.current',
        'doctrine.migrations_cmd.diff',
        'doctrine.migrations_cmd.dumpschema',
        'doctrine.migrations_cmd.execute',
        'doctrine.migrations_cmd.generate',
        'doctrine.migrations_cmd.latest',
        'doctrine.migrations_cmd.list',
        'doctrine.migrations_cmd.migrate',
        'doctrine.migrations_cmd.rollup',
        'doctrine.migrations_cmd.status',
        'doctrine.migrations_cmd.syncmetadatastorage',
        'doctrine.migrations_cmd.version',
        'doctrine.migrations_cmd.uptodate',
    ];

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        if (! class_exists(ImportCommand::class)) {
            return;
        }

        $this->commands[] = 'doctrine.dbal_cmd.import';
    }

    public function configure(Application $cli): void
    {
        $commands = $this->getAvailableCommands();
        foreach ($commands as $commandName) {
            $command = $this->container->get($commandName);
            $command->getDefinition()->addOption($this->createObjectManagerInputOption());

            $cli->add($command);
        }

        $objectManager = $this->container->get($this->getObjectManagerName());

        $helpers = $this->getHelpers($objectManager);
        foreach ($helpers as $name => $instance) {
            $cli->getHelperSet()->set($instance, $name);
        }
    }

    /**
     * @return array<string,Helper>
     */
    private function getHelpers(EntityManagerInterface $objectManager): array
    {
        $helpers = [
            'dialog' => new QuestionHelper(),
            'em' => new EntityManagerHelper($objectManager),
        ];

        // this is only available with DBAL 2.x
        if (class_exists(ConnectionHelper::class)) {
            $helpers['db'] = new ConnectionHelper($objectManager->getConnection());
        }

        return $helpers;
    }

    private function createObjectManagerInputOption(): InputOption
    {
        return new InputOption(
            'object-manager',
            null,
            InputOption::VALUE_OPTIONAL,
            'The name of the object manager to use.',
            $this->defaultObjectManagerName
        );
    }

    private function getObjectManagerName(): string
    {
        $arguments = new ArgvInput();

        if (! $arguments->hasParameterOption('--object-manager')) {
            return $this->defaultObjectManagerName;
        }

        /** @psalm-suppress ReservedWord */
        return $arguments->getParameterOption('--object-manager');
    }

    /**
     * @return string[]
     */
    private function getAvailableCommands(): array
    {
        if (class_exists(VersionCommand::class)) {
            return ArrayUtils::merge($this->commands, $this->migrationCommands);
        }

        return $this->commands;
    }
}
