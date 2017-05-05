<?php

class SystemDateTime
{
	static public function date( $format = 'Y-m-d H:i:s' )
	{
		return self::serverDate($format);
	}

	static public function convertToClientTime( $date, $format = 'Y-m-d H:i:s' )
	{
		if ( $date == '' ) return $date;
		
		$offset = EnvironmentSettings::getUTCOffset();

		$utc_offset = -1 * (($offset > 0) - ($offset < 0)) * $offset;
		
		try
		{
            if ( strpos($date, '00:00:00') > 0 ) {
                $time = new DateTime($date);
                return $time->format($format);
            }

			// convert from server's time to UTC+0
			$time = new DateTime($date." ".$utc_offset." hours", new DateTimeZone("UTC"));
			
			// convert from UTC+0 to client's time
			$time->setTimezone(EnvironmentSettings::getClientTimeZone());

			return $time->format($format);
		}
		catch( Exception $e )
		{
			try
			{
				$time = new DateTime($date." ".$utc_offset." hours", new DateTimeZone("UTC"));
	
				return $time->format($format);
			}
			catch( Exception $e )
			{
				return "";
			}
		}
	}
	
	static public function convertToServerTime( $date, $format = 'Y-m-d H:i:s' )
	{
		if ( $date == '' ) return $date;

		try
		{
            if ( strpos($date, '00:00:00') > 0 ) {
                $time = new DateTime($date);
                return $time->format($format);
            }

			// convert from client's time to UTC+0
			$time = new DateTime($date, EnvironmentSettings::getClientTimeZone());
			
			$time->setTimezone(new DateTimeZone("UTC"));

			// convert from UTC+0 to server's time
			$time->add(DateInterval::createFromDateString(EnvironmentSettings::getUTCOffset()." hours"));

			return $time->format($format);
		}
		catch( Exception $e )
		{
			try
			{
				$time = new DateTime($date, new DateTimeZone("UTC"));
	
				// convert from UTC+0 to server's time
				$time->add(DateInterval::createFromDateString(EnvironmentSettings::getUTCOffset()." hours"));
				
				return $time->format($format);
			}
			catch( Exception $e )
			{
				return "";
			}
		}
	}
	
	static private function serverDate( $format = 'Y-m-d H:i:s' )
	{
		$time = new DateTime(EnvironmentSettings::getUTCOffset()." hours", new DateTimeZone("UTC")); 
		
		return $time->format($format);
	}

	static public function getTimeParseRegex()
	{
		return text(2115);
	}
}