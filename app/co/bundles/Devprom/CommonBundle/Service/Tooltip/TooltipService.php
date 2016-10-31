<?php

namespace Devprom\CommonBundle\Service\Tooltip;

class TooltipService
{
	private $object_it;
	private $extended = false;
	
	public function __construct( $class_name, $object_id, $extended = false )
	{
		$this->extended = $extended;
		$object = getFactory()->getObject($class_name);
		$this->extendModel($object);
    	$this->setObjectIt($object->getExact($object_id));
	}
	
	public function setObjectIt( $object_it ) {
		$this->object_it = $object_it;
	}
	
	public function getObjectIt() {
		return $this->object_it;
	}

	public function getExtended() {
        return $this->extended;
    }
			
    public function getData()
    {
		if ( $this->object_it->getId() < 1 ) return array();
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
		if ( $this->extended ) {
			$tooltip_attributes = array_diff(
				array_merge(
					$tooltip_attributes,
					$object->getAttributesByGroup('trace'),
					$object->getAttributesVisible()
				),
				array (
					'OrderNum', 'RecordCreated', 'RecordModified', 'UID'
				)
			);
		}
		else {
			$tooltip_attributes = array_merge(
				array('Caption', 'Description'),
				$tooltip_attributes
			);
		}
	    $system_attributes = $object->getAttributesByGroup('system');
 		
 		foreach ( $object->getAttributes() as $attribute => $parms )
 	 	{
 	 		if ( $attribute == 'State' ) continue;
			if ( in_array($attribute, $system_attributes) ) continue;
			if ( $object_it->get($attribute) == '' ) continue;

 	 		$type = $object->getAttributeType($attribute);
 	 		if ( $type == '' ) continue;

 	 		if ( !in_array($attribute, $tooltip_attributes) ) continue;

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
			    $totext = new \Html2Text\Html2Text( $object_it->getHtmlDecoded($attribute), array('width'=>0) );
			    return $object_it->getWordsOnlyValue($totext->getText(), 25);

 			case 'wysiwyg':
			    return $object_it->getHtmlDecoded($attribute);

 			case 'date':
            case 'datetime':
			    return $object_it->getDateFormatShort($attribute);
			    
 			default:
	 	 		if ( $object_it->object->IsReference($attribute) )
		 		{	
					$ref_it = $object_it->getRef($attribute);
					$titles = array();

                    if ( $ref_it->object instanceof \Attachment ) {
                        while( !$ref_it->end() )
                        {
                            if ( $ref_it->IsImage('File') ) {
                                $titles[] = '<img class="wiki_page_image" src="'.$ref_it->getFileUrl().'">';
                            }
                            $ref_it->moveNext();
                        }
                        if ( count($titles) == 1 ) $titles[] = '';
                    }
                    else {
                        $uid = new \ObjectUID;
                        while( !$ref_it->end() )
                        {
                            $title = $this->extended ? $uid->getUidWithCaption($ref_it) : $uid->getUidTitle($ref_it);
                            if ( $ref_it->object instanceof \MetaobjectStatable ) {
                                $title .= ' ('.$ref_it->getStateIt()->getDisplayName().')';
                            }
                            $titles[] = $title;
                            $ref_it->moveNext();
                        }
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