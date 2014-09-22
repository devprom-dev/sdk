<?php

class FieldConnectorsDictionary extends FieldDictionary
{
    function getOptions()
    {
        $options = array();
        
        $object = $this->getObject();
        
        $connectors = $object->getConnectors();
        
		foreach( $connectors as $connector ) 
		{ 
		    $options[] = array (
		        'value' => strtolower(get_class($connector)),
		        'caption' => $connector->getDisplayName()
		    );
		} 
        
		return $options;
    }
}