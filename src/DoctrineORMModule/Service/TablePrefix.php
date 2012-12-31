<?php

// ********************************************************************************

// http://docs.doctrine-project.org/en/2.0.x/cookbook/sql-table-prefixes.html

// ********************************************************************************

namespace DoctrineORMModule\Service;

// ********************************************************************************

use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;

// ********************************************************************************

class TablePrefix
{
  // ****************************************************************************
	
	protected $prefix = '';

	// ********************************************************************************
	
    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    // ********************************************************************************
    
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $classMetadata->setTableName($this->prefix . $classMetadata->getTableName());
        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY) {
                $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }
    }

    // ********************************************************************************
}
