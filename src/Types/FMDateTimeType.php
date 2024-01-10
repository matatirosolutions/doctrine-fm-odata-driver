<?php

namespace MSDev\DoctrineFMODataDriver\Types;

use DateTimeInterface;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;


class FMDateTimeType extends Type
{
    protected $name = 'fmdatetime';

    public function getName(): string
    {
        return $this->name;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return $value;
        }

        if(is_int($value) && $value > 0) {
            return date(DateTimeInterface::ATOM, $value);
        }

        if($value instanceof \DateTime) {
            return $value->format(DateTimeInterface::ATOM);
        }


        throw ConversionException::conversionFailed(var_export($value, true), $this->name);
    }

    /**
     * @return mixed
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        $date = \DateTime::createFromFormat(DateTimeInterface::ATOM, $value);
        if($date && $date->format('Y-m-d\TH:i:s\Z') === $value) {
            return $date;
        }

        throw ConversionException::conversionFailed(var_export($value, true), $this->name);
    }

}
