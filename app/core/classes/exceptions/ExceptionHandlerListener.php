<?php

abstract class ExceptionHandlerListener
{
	abstract function handle( $data, $code );
}