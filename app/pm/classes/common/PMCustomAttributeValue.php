<?php
include "predicates/CustomAttributeValueVpdPredicate.php";

class PMCustomAttributeValue extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_AttributeValue');
        $this->addAttributeGroup('CustomAttribute', 'alternative-key');
        $this->addAttributeGroup('ObjectId', 'alternative-key');
 	}
}