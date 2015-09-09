<?php

abstract class ExceptionHandlerListener
{
	abstract public function handle( $data, $code );
	abstract public function captureException( $e );
}