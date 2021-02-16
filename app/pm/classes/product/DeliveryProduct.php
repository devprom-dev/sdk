<?php

include "DeliveryProductRegistry.php";

class DeliveryProduct extends Metaobject
{
    function __construct()
    {
        parent::__construct('entity', new DeliveryProductRegistry($this));
    }
}