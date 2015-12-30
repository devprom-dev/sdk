<?php

abstract class MailerSettingsFile
{
	abstract public function read( $parameter );
	
	abstract public function write( $parameter, $value );
	
	abstract public function exists();
}