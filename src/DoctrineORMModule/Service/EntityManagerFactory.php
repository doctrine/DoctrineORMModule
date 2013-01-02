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
use DoctrineORMModule\Listener\TablePrefixListener;

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

		$cfg = $sl->get('Config');
		$cfg = $cfg['doctrine']['connection']['orm_default']['params'];

		/**
		 * @todo make naming strategy interface useable
		 * http://docs.doctrine-project.org/en/latest/reference/namingstrategy.html
		 */
		if (isset($cfg['nameing_strategy']))
		{
			switch ($cfg['nameing_strategy'])
			{
				case 'underscore_case_lower':
					$namingStrategy = new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy(CASE_LOWER);
					$config->setNamingStrategy($namingStrategy);
					break;
				case 'UNDERSCORE_CASE_UPPER':
					$namingStrategy = new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy(CASE_UPPER);
					$config->setNamingStrategy($namingStrategy);
					break;
			}
		}
		
		if (isset($cfg['tableprefix']) && $cfg['tableprefix'] != '')
		{
			$evm = $connection->getEventManager();
			$tablePrefixListener = new TablePrefixListener($cfg['tableprefix']);
			$evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefixListener);
			return EntityManager::create($connection, $config, $evm);
		}

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
