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

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     * @return EntityManager
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        /* @var $options \DoctrineORMModule\Options\EntityManager */
        $options = $this->getOptions($sl, 'entitymanager');
        $connection = $sl->get($options->getConnection());
        $config     = $sl->get($options->getConfiguration());

    	// Table Prefix
    	// Test 2
		// http://docs.doctrine-project.org/en/2.0.x/cookbook/sql-table-prefixes.html
		$cfg = $sl->get('Config');
		$cfg = $cfg['doctrine']['connection']['orm_default']['params'];

		if (isset($cfg['tableprefix']))
		{
			$evm = $connection->getEventManager();
			$tablePrefix = new TablePrefix($cfg['tableprefix']);
			$evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);
			return EntityManager::create($connection, $config, $evm);
		}
		// Table Prefix

        return EntityManager::create($connection, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\EntityManager';
    }
}
