<?php

class Devprom404Table extends BaseDEVPROMTable
{
 	public function draw()
 	{		
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		
		parent::draw();
 	}
}