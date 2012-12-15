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
    ),
);
