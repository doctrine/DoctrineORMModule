<?php

namespace DoctrineORMModule\Diagnostics;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Connections\MasterSlaveConnection;
use Doctrine\DBAL\Sharding\PoolingShardConnection;
use ZendDiagnostics\Check\AbstractCheck;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

class CheckConnection extends AbstractCheck
{
    /** @var Connection */
    private $connection;

    /**
     * CheckOrmDefaultEntityManager constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * @return ResultInterface
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function check()
    {
        if ($this->connection instanceof MasterSlaveConnection) {
            return $this->checkMasterSlaveConnection($this->connection);
        }
        
        if ($this->connection instanceof PoolingShardConnection) {
            return $this->checkPoolingShardConnection($this->connection);
        }
        
        return $this->checkStandardConnection($this->connection);
    }

    /**
     * @param Connection $connection
     * @return Success|Failure
     */
    private function checkStandardConnection(Connection $connection)
    {
        if ($connection->ping()) {
            return new Success(get_class($connection));
        }

        return new Failure(get_class($connection));
    }

    /**
     * @param MasterSlaveConnection $connection
     * @return Success|Failure
     */
    private function checkMasterSlaveConnection(MasterSlaveConnection $connection)
    {
        // TODO Check all slaves, instead of random one if possible.
        
        $connection->connect('slave');
        $isSlaveConnected = $connection->ping();

        $connection->connect('master');
        $isMasterConnected = $connection->ping();

        $data = [
            'slave' => $isSlaveConnected ? 'connected' : 'not connected',
            'master' => $isMasterConnected ? 'connected' : 'not connected',
        ];

        if ($isMasterConnected && $isSlaveConnected) {
            return new Success(get_class($connection), $data);
        }

        return new Failure(get_class($connection), $data);
    }

    /**
     * @param PoolingShardConnection $connection
     * @return Success|Failure
     */
    private function checkPoolingShardConnection(PoolingShardConnection $connection)
    {
        // TODO Check all shards, instead of just the active one.
        
        $isConnected = $connection->ping();
        
        $data = [
            $connection->getActiveShardId() => $isConnected ? 'connected' : 'not connected'
        ];

        if ($isConnected) {
            return new Success(get_class($connection), $data);
        }

        return new Failure(get_class($connection), $data);
    }
}
