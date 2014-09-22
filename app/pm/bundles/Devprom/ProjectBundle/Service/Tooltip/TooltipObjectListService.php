<?php

namespace Devprom\ProjectBundle\Service\Tooltip;

class TooltipObjectListService
{
	private $object_it;
	
	public function __construct( $class_name, $object_id )
	{
		$object = getFactory()->getObject($class_name);
		
	 	if ( $object instanceof \MetaobjectStatable ) $object->addSort( new \SortAttributeClause('State') );
 		
    	$this->object_it = $object->getExact(preg_split('/,/',$object_id));
	}
	
    public function getData()
    {
    	$uid = new \ObjectUID;
    	
    	$objects = array();
    	
    	while( !$this->object_it->end() )
    	{
    		$objects[] = array (
    				'ref' => $uid->getUidWithCaption($this->object_it)
    		);
    		
    		$this->object_it->moveNext(); 
    	}
    	
    	return array (
    			'objects' => $objects
    	);
    }
}