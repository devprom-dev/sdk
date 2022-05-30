<?php

class SystemSettingsTimezoneMapping
{
	public function map( Metaobject $object, array & $parms )
	{
        if ( !preg_match('/[+|-]\d{2,}/', $parms['TimeZoneUTC'], $matches ) ) {
            $parms['TimeZoneUTC'] = '+00';
        }
	}
}