<?php

class LoggerAppenderText extends LoggerAppenderNull
{
	private $text;

	public function getText()
	{
		return $this->text;
	}
	
	public function append( LoggerLoggingEvent $event )
	{
		$this->text .= $event->getMessage().'<br/>';
	}
}