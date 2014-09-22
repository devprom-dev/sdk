<?php

namespace Devprom\CommonBundle\Doctrine\DBAL\Type;

use \Doctrine\DBAL\Types\DateTimeType;
use \Doctrine\DBAL\Platforms\AbstractPlatform;
use \Doctrine\DBAL\Types\ConversionException;

class UTCDateTimeType extends DateTimeType
{
    static private $utc = null;

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) return null;
        
        if ($value->format() == "") return \SystemDateTime::date();
        
        return \SystemDateTime::convertToServerTime( $value->format($platform->getDateTimeFormatString()) );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ( strtotime($value) < 1 ) return null;
        
        $value = \SystemDateTime::convertToClientTime($value);
        
        $val = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            (self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC'))
        );
        
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
        return $val;
    }
}