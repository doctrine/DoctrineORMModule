<?php

return array(

    'service_manager' => array(
        'factories' => array(
            'doctrine.orm_diagnostics.connection' => '\DoctrineORMModule\Diagnostics\CheckConnectionFactory',
            'doctrine.orm_diagnostics.schema' => '\DoctrineORMModule\Diagnostics\CheckSchemaFactory',
        ),
    ),
    
    'diagnostics' => array(
        'DoctrineORMModule' => array(
            'Database Connection' => 'doctrine.orm_diagnostics.connection',
            'ORM Validate Schema' => 'doctrine.orm_diagnostics.schema',
        ),
    ),
);
