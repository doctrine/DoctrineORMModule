# Extra goodies included with DoctrineModule
The items listed below are entirely optional and are intended to enhance integration between Zend Framework 2 and
Doctrine 2 .

## ObjectExists and NoObjectExists Validators
The ObjectExists and NoObjectExists are validators similar to Zend\Validator\Db validators. You can
pass a variety of options to determine validity. The most basic use case requires an entity manager (em),
an entity, and a field. You also have the option of specifying a query_builder Closure to use if you
want to fine tune the results.

```php
<?php
$validator = new \DoctrineModule\Validator\NoObjectExists(array(
    // object repository to lookup
    'object_repository' => $serviceLocator->get('Doctrine\ORM\EntityManager')->getRepository('My\Entity\User'),

    // fields to match
    'fields' => array('username'),
));

// following works also with simple values if the number of fields to be matched is 1
echo $validator->isValid(array('username' => 'test')) ? 'Valid' : 'Invalid! Duplicate found!';
```

## Authentication adapter for Zend\Authentication
The authentication adapter is intended to provide an adapter for `Zend\Authentication`. It works much
like the `DbTable` adapter in the core framework. You must provide the entity manager instance,
entity name, identity field, and credential field. You can optionally provide a callable method
to perform hashing on the password prior to checking for validation.

```php
<?php
$adapter = new \DoctrineModule\Authentication\Adapter\DoctrineObject(
    $this->getLocator()->get('Doctrine\ORM\EntityManager'),
    'Application\Test\Entity',
    'username', // optional, default shown
    'password',  // optional, default shown,
    function($identity, $credential) { // optional callable
        return \My\Service\User::hashCredential(
            $credential,
            $identity->getSalt(),
            $identity->getAlgorithm()
        );
    }
);

$adapter->setIdentityValue('admin');
$adapter->setCredentialValue('pa55w0rd');
$result = $adapter->authenticate();

echo $result->isValid() ? 'Authenticated!' : 'Could not authenticate';
```

## Custom DBAL Types
To register custom Doctrine DBAL types, simply add them to the `doctrine.configuration.my_dbal_default.types`
key in you configuration file:

```php
<?php
return array(
    'doctrine' => array(
        'configuration' => array(
            'my_dbal_default' => array(
                'types' => array(
                    // You can override a default type
                    'date' => 'My\DBAL\Types\DateType',

                    // And set new ones
                    'tinyint' => 'My\DBAL\Types\TinyIntType',
                ),
            ),
        ),
    ),
);
```

You are now able to use them, for example, in your ORM entities:

```php
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
```

To have Schema-Tool convert the underlying database type of your new "tinyint" directly into an instance 
of TinyIntType you have to additionally register this mapping with your database platform.

```php
<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'doctrine_type_mappings' => array(
                    'tinyint' => 'tinyint',
                ),
            ),
        ),
    ),
);
```

Now using Schema-Tool, whenever it detects a column having the "tinyint" it will convert it into a "tinyint"
Doctrine Type instance for Schema representation. Keep in mind that you can easily produce clashes this 
way, each database type can only map to exactly one Doctrine mapping type.