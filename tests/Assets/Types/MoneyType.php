<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use stdClass;

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

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): object
    {
        return new stdClass();
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        return $value->toDecimal();
    }

    public function getName(): string
    {
        return self::MONEY;
    }
}
