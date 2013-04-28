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
        //'Doctrine\ORM\EntityManager' => 'doctrine.entitymanager.orm_default',
    ),
    'factories' => array(
        'doctrine.migrations_configuration.orm_default' => function ($serviceManager) {
            $connection       = $serviceManager->get('doctrine.connection.orm_default');
            $appConfig        = $serviceManager->get('Config');
            $migrationsConfig = $appConfig['doctrine']['migrations'];
            $configuration    = new \Doctrine\DBAL\Migrations\Configuration\Configuration($connection);

            $configuration->setMigrationsDirectory($migrationsConfig['directory']);
            $configuration->setMigrationsNamespace($migrationsConfig['namespace']);
            $configuration->setMigrationsTableName($migrationsConfig['table']);
            $configuration->registerMigrationsFromDirectory($migrationsConfig['directory']);

            return $configuration;
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
