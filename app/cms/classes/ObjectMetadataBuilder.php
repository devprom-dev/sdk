<?php

include "model/ObjectMetadata.php";

abstract class ObjectMetadataBuilder
{
    abstract public function build( ObjectMetadata $metadata );
}