#### How to Register Custom DQL Functions

```php
return array(    
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'numeric_functions' => array(
                    'ROUND' => 'path\to\my\query\round'
                )
            )
        ),
    ),
)
```
