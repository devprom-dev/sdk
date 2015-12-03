<?php

namespace Devprom\CommonBundle\Service\Tooltip;

include_once SERVER_ROOT_PATH.'ext/html/html2text.php';

class TooltipService
{
	private $object_it;
	
	public function __construct( $class_name, $object_id )
	{
		$object = getFactory()->getObject($class_name);
		$this->extendModel($object);
    	$this->setObjectIt($object->getExact($object_id));
	}
	
	public function setObjectIt( $object_it )
	{ 
		$this->object_it = $object_it;
	}
	
	public function getObjectIt()
	{
		return $this->object_it;
	}
			
    public function getData()
    {
    	return array (
    			'attributes' => 
    				$this->buildAttributes( $this->object_it )
    	);
    }
    
    protected function extendModel( $object )
    {
    }
    
    protected function buildAttributes( $object_it )
    {
    	$data = array();
    	
    	$object = $object_it->object;
    	
    	$tooltip_attributes = $object->getAttributesByGroup('tooltip');
	    $system_attributes = $object->getAttributesByGroup('system');
 		
 		foreach ( $object->getAttributes() as $attribute => $parms )
 	 	{
 	 		if ( $attribute == 'State' ) continue;
			if ( in_array($attribute, $system_attributes) ) continue;
			if ( $object_it->get($attribute) == '' ) continue;

 	 		$type = $object->getAttributeType($attribute);
 	 		if ( $type == '' ) continue;

 	 		if ( !$object->IsAttributeVisible($attribute) && !in_array($attribute, $tooltip_attributes) ) continue;

 	 		$data[] = array (
 	 				'name' => $attribute,
 	 				'title' => translate($object->getAttributeUserName($attribute)), 
 	 				'type' => $type,
 	 				'text' => $this->getAttributeValue( $object_it, $attribute, $type ) 
 	 		); 
 	 	}

 	 	return $data;
    }

 	protected function getAttributeValue( $object_it, $attribute, $type )
 	{
 	    switch ( $attribute )
 	    {
 	        case 'State':
 	        	return $object_it->get('StateName');
 	    }
 	    
 		switch ( $type )
 		{
			case 'char':
			    return $object_it->get($attribute) == 'Y' ? translate('Да') : translate('Нет');
			    
 		    case 'text':
			    $totext = new \html2text( $object_it->getHtmlDecoded($attribute) );
			    return $object_it->getWordsOnlyValue($totext->get_text(), 25);

 			case 'wysiwyg':
			    return $object_it->getHtmlDecoded($attribute);

 			case 'date':
			    return $object_it->getDateFormat($attribute);
			    
 			default:
	 	 		if ( $object_it->object->IsReference($attribute) )
		 		{	
		 			$uid = new \ObjectUID;
		 			
					$ref_it = $object_it->getRef($attribute);
					$titles = array();
					
					while( !$ref_it->end() )
					{
						$titles[] = $uid->getUidTitle($ref_it); 
						$ref_it->moveNext();
					}
					
		 			return (count($titles) > 1 ? '<br/>' : '').join('<br/>', $titles);
		 		}
		 		else
		 		{
 					return $object_it->getHtml($attribute);
		 		}
 	 	}
 	}	
}