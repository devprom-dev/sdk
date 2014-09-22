<?php

class PMCustomAttributeIterator extends OrderedIterator
{
    function get2( $attr )
    {
        switch ( $attr )
        {
            case 'ReferenceName': return strtolower(parent::get($attr));

            default: return parent::get($attr); 
        }
    }
    
 	function toReferenceNames()
 	{
 		$names = array();
 		while ( !$this->end() )
 		{
 			array_push( $names, $this->get('ReferenceName')); 
 			$this->moveNext();
 		}
 		return $names;
 	}
 	
 	function toDictionary()
 	{
 		$lov = array();
 		
 		$lines = preg_split('/\n\r?/', $this->get('ValueRange'));

 		foreach( $lines as $line )
 		{
 			if ( trim($line) == '' ) continue;
 			
 			$parts = preg_split('/:/', $line );
 			
 			$lov[trim($parts[0], ' '.chr(10))] = trim($parts[1]);
 		}
 		
 		return $lov;
 	}
 	
 	function getEntityDisplayName()
 	{
 		return $this->object->getEntityDisplayName($this->get('EntityReferenceName'), $this->get('ObjectKind'));
 	}
}
