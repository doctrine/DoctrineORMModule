Miscellaneous 
=============

The items listed below are optional and intended to enhance 
integration between Laminas and Doctrine ORM.

Authentication Adapter
----------------------

The authentication adapter is intended to provide an adapter for ``Laminas\Authentication``. It works much
like the ``DbTable`` adapter in the core framework. You must provide the
entity manager instance, entity name, identity field, and credential
field. You can optionally provide a callable method to perform hashing
on the password prior to checking for validation.

.. code:: php

    <?php
    
    use DoctrineModule\Authentication\Adapter\DoctrineObject as DoctrineObjectAdapter;
    
    $adapter = DoctrineObjectAdapter(
        $entityManager,
        'Application\Test\Entity',
        'username', // optional, default shown
        'password',  // optional, default shown,
        function($identity, $credential) { // optional callable
            return \Application\Service\User::hashCredential(
                $credential,
                $identity->getSalt(),
                $identity->getAlgorithm()
            );
        }
    );

    $adapter->setIdentityValue('admin');
    $adapter->setCredentialValue('password');
    $result = $adapter->authenticate();

    echo $result->isValid() ? 'Authenticated' : 'Could not authenticate';


Custom DBAL Types
-----------------

To register custom Doctrine DBAL types add them to the
``doctrine.configuration.orm_default.types`` key in you
configuration file:

.. code:: php

    <?php
    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'types' => [
                        // You can override a default type
                        'date' => 'My\DBAL\Types\DateType',

                        // And set new ones
                        'tinyint' => 'My\DBAL\Types\TinyIntType',
                    ],
                ],
            ],
        ],
    ];

With this configuration you may use them in your ORM entities
to define field datatypes:

.. code:: php

    <?php

    class User
    {
        /**
         * @ORM\Column(type="date")
         */
        protected $birthdate;

        /**
         * @ORM\Column(type="tinyint")
         */
        protected $houses;
    }

To have Schema-Tool convert the underlying database type of your new
"tinyint" directly into an instance of TinyIntType you have to
additionally register this mapping with your database platform.

.. code:: php

    <?php
    return [
        'doctrine' => [
            'connection' => [
                'orm_default' => [
                    'doctrine_type_mappings' => [
                        'tinyint' => 'tinyint',
                    ],
                ],
            ],
        ],
    ];

Now using Schema-Tool, whenever it finds a column of type "tinyint"
it will convert it into a "tinyint" Doctrine Type instance for Schema
representation. Keep in mind that you can easily produce clashes this
way because each database type can only map to exactly one Doctrine mapping
type.
