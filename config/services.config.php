<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

return array(
    'aliases' => array(
        'Doctrine\ORM\EntityManager' => 'doctrine.entitymanager.orm_default',
    ),
    'factories' => array(

        'doctrine.authenticationadapter.orm_default'  => new DoctrineModule\Service\Authentication\AdapterFactory('orm_default'),
        'doctrine.authenticationstorage.orm_default'  => new DoctrineModule\Service\Authentication\StorageFactory('orm_default'),
        'doctrine.authenticationservice.orm_default'  => new DoctrineModule\Service\Authentication\AuthenticationServiceFactory('orm_default'),

        'doctrine.connection.orm_default'             => new DoctrineORMModule\Service\DBALConnectionFactory('orm_default'),
        'doctrine.configuration.orm_default'          => new DoctrineORMModule\Service\ConfigurationFactory('orm_default'),
        'doctrine.entitymanager.orm_default'          => new DoctrineORMModule\Service\EntityManagerFactory('orm_default'),

        'doctrine.driver.orm_default'                 => new DoctrineModule\Service\DriverFactory('orm_default'),
        'doctrine.eventmanager.orm_default'           => new DoctrineModule\Service\EventManagerFactory('orm_default'),
        'doctrine.entity_resolver.orm_default'        => new DoctrineORMModule\Service\EntityResolverFactory('orm_default'),
        'doctrine.sql_logger_collector.orm_default'   => new DoctrineORMModule\Service\SQLLoggerCollectorFactory('orm_default'),
        'doctrine.mapping_collector.orm_default'      => function (Zend\ServiceManager\ServiceLocatorInterface $sl) {
            $em = $sl->get('doctrine.entitymanager.orm_default');

            return new DoctrineORMModule\Collector\MappingCollector($em->getMetadataFactory(), 'orm_default_mappings');
        },
        'DoctrineORMModule\Form\Annotation\AnnotationBuilder' => function(Zend\ServiceManager\ServiceLocatorInterface $sl) {
            return new DoctrineORMModule\Form\Annotation\AnnotationBuilder($sl->get('doctrine.entitymanager.orm_default'));
        },

		'doctrine.migrations_configuration' => function ($serviceManager) {
			$connection = $serviceManager->get('doctrine.connection.orm_default');

			$appConfig = $serviceManager->get('Config');
			$migrationsConfig = $appConfig['doctrine']['migrations'];

			$configuration = new \Doctrine\DBAL\Migrations\Configuration\Configuration($connection);
			$configuration->setMigrationsDirectory($migrationsConfig['directory']);
			$configuration->setMigrationsNamespace($migrationsConfig['namespace']);
			$configuration->setMigrationsTableName($migrationsConfig['table']);
			$configuration->registerMigrationsFromDirectory($migrationsConfig['directory']);

			return $configuration;
		},
		'doctrine.helper_set' => function ($serviceManager) {
			$connection = $serviceManager->get('doctrine.connection.orm_default');

			$entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

			$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
				'dialog' => new \Symfony\Component\Console\Helper\DialogHelper(),
				'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($connection),
				'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
			));

			return $helperSet;
		},
		'doctrine.cliapp' => function ($serviceManager) {
			$helperSet = $serviceManager->get('doctrine.helper_set');

			$cli = new \Symfony\Component\Console\Application('Doctrine ORM Module con', \DoctrineTools\Version::VERSION);
			$cli->setCatchExceptions(true);
			$cli->setAutoExit(false);
			$cli->setHelperSet($helperSet);
			$cli->addCommands(array(
				$serviceManager->get('doctrine.cmd.dbal.runsql'),
				$serviceManager->get('doctrine.cmd.dbal.import'),

				$serviceManager->get('doctrine.cmd.orm.clear-cache.metadata'),
				$serviceManager->get('doctrine.cmd.orm.clear-cache.result'),
				$serviceManager->get('doctrine.cmd.orm.clear-cache.query'),
				$serviceManager->get('doctrine.cmd.orm.schema-tool.create'),
				$serviceManager->get('doctrine.cmd.orm.schema-tool.update'),
				$serviceManager->get('doctrine.cmd.orm.schema-tool.drop'),
				$serviceManager->get('doctrine.cmd.orm.ensure-production-settings'),
				$serviceManager->get('doctrine.cmd.orm.convert-d1-schema'),
				$serviceManager->get('doctrine.cmd.orm.generate-repositories'),
				$serviceManager->get('doctrine.cmd.orm.generate-entities'),
				$serviceManager->get('doctrine.cmd.orm.generate-proxies'),
				$serviceManager->get('doctrine.cmd.orm.convert-mapping'),
				$serviceManager->get('doctrine.cmd.orm.run-dql'),
				$serviceManager->get('doctrine.cmd.orm.validate-schema'),
				$serviceManager->get('doctrine.cmd.orm.info'),

				$serviceManager->get('doctrine.cmd.migrations.execute'),
				$serviceManager->get('doctrine.cmd.migrations.generate'),
				$serviceManager->get('doctrine.cmd.migrations.migrate'),
				$serviceManager->get('doctrine.cmd.migrations.status'),
				$serviceManager->get('doctrine.cmd.migrations.version'),
				$serviceManager->get('doctrine.cmd.migrations.diff'),
			));

			return $cli;
		},
		// Migrations commands
		'doctrine.cmd.migrations.generate' => new \DoctrineORMModule\Service\MigrationsCommandFactory('generate'),
		'doctrine.cmd.migrations.execute' => new \DoctrineORMModule\Service\MigrationsCommandFactory('execute'),
		'doctrine.cmd.migrations.migrate' => new \DoctrineORMModule\Service\MigrationsCommandFactory('migrate'),
		'doctrine.cmd.migrations.status' => new \DoctrineORMModule\Service\MigrationsCommandFactory('status'),
		'doctrine.cmd.migrations.version' => new \DoctrineORMModule\Service\MigrationsCommandFactory('version'),
		'doctrine.cmd.migrations.diff' => new \DoctrineORMModule\Service\MigrationsCommandFactory('diff'),
    ),

	'invokables' => array(
		// DBAL commands
		'doctrine.cmd.dbal.runsql' => '\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand',
		'doctrine.cmd.dbal.import' => '\Doctrine\DBAL\Tools\Console\Command\ImportCommand',
		// ORM Commands
		'doctrine.cmd.orm.clear-cache.metadata' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand',
		'doctrine.cmd.orm.clear-cache.result' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand',
		'doctrine.cmd.orm.clear-cache.query' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand',
		'doctrine.cmd.orm.schema-tool.create' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand',
		'doctrine.cmd.orm.schema-tool.update' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand',
		'doctrine.cmd.orm.schema-tool.drop' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand',
		'doctrine.cmd.orm.ensure-production-settings' => '\Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand',
		'doctrine.cmd.orm.convert-d1-schema' => '\Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand',
		'doctrine.cmd.orm.generate-repositories' => '\Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand',
		'doctrine.cmd.orm.generate-entities' => '\Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand',
		'doctrine.cmd.orm.generate-proxies' => '\Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand',
		'doctrine.cmd.orm.convert-mapping' => '\Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand',
		'doctrine.cmd.orm.run-dql' => '\Doctrine\ORM\Tools\Console\Command\RunDqlCommand',
		'doctrine.cmd.orm.validate-schema' => '\Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand',
		'doctrine.cmd.orm.info' => '\Doctrine\ORM\Tools\Console\Command\InfoCommand',
	)
);
