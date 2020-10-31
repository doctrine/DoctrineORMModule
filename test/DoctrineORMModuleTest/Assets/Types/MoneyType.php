<?php

namespace DoctrineORMModuleTest\Assets\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * My custom datatype.
 */
class MoneyType extends Type
{
    public const MONEY = 'money';

    /**
     * @param mixed[] $fieldDeclaration
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'MyMoney';
    }

    /**
     * @param mixed $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): Money
    {
        return new Money($value);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->toDecimal();
    }

    public function getName(): string
    {
        return self::MONEY;
    }
}
