<?php
namespace SpiffyDoctrineORM\Doctrine\ORM;
use PDO,
    Doctrine\DBAL\DriverManager,
    SpiffyDoctrine\Doctrine\Common\EventManager,
    SpiffyDoctrine\Doctrine\Instance;

class Connection extends Instance
{
	/**
	 * @var Doctrine\ORM\Configuration
	 */
	protected $config;
	
	/**
	 * @var Doctrine\Common\EventManager
	 */
	protected $evm;
	
	/**
	 * Constructor
	 * 
	 * @param array 		$opts
	 * @param Configuration $config
	 * @param EventManager  $evm
	 * @param PDO 			$pdo
	 */
	public function __construct(array $params, Configuration $config, EventManager $evm, PDO $pdo = null)
	{
		if ($pdo) {
			$params['pdo'] = $pdo;
		}
		
		$this->config = $config->getInstance();
		$this->evm    = $evm->getInstance();
		
		parent::__construct($params);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see SpiffyDoctrineORM\Instance.Instance::loadInstance()
	 */
	protected function loadInstance()
	{
        $this->instance = DriverManager::getConnection(
            $this->opts,
            $this->config,
            $this->evm
        );		
	}
}