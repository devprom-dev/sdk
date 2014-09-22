<?php

abstract class VersionedObjectRegistryBuilder
{
	abstract public function build( VersionedObjectRegistry & $registry );
}