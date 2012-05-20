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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModule;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Zend\ModuleManager\ModuleManager;

/**
 * Base module for Doctrine ORM.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   1.0
 * @version $Revision$
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class Module
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Expected to return \Zend\ServiceManager\Configuration object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Configuration
     */
    public function getServiceConfiguration()
    {
        return array(
            'aliases' => array(
                'doctrine_orm_metadata_cache'  => 'Doctrine\Common\Cache\ArrayCache',
                'doctrine_orm_query_cache'     => 'Doctrine\Common\Cache\ArrayCache',
                'doctrine_orm_result_cache'    => 'Doctrine\Common\Cache\ArrayCache',
            ),
            'factories' => array(
                'doctrine_orm_cli'                 => 'DoctrineORMModule\Service\CliFactory',
                'Doctrine\Common\Cache\ArrayCache' => function() { return new ArrayCache; },
                'Doctrine\ORM\Configuration'       => 'DoctrineORMModule\Service\ConfigurationFactory',
                'Doctrine\ORM\EntityManager'       => 'DoctrineORMModule\Service\EntityManagerFactory',
            )
        );
    }
}
