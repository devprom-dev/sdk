<?php
namespace Devprom\CommonBundle\Service\Tooltip;

define ('TOOLTIP_GROUP_TRACE', 4);
define ('TOOLTIP_GROUP_WORKFLOW', 3);
define ('TOOLTIP_GROUP_ADDITIONAL', 2);

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

		$attributes = $this->buildAttributes( $this->object_it );
        usort($attributes, function($left, $right) {
            return $left['group'] > $right['group'];
        });

        return array (
            'attributes' => $attributes,
            'groups' => array(
                1 => translate('Сроки'),
                TOOLTIP_GROUP_ADDITIONAL => translate('Дополнительно'),
                TOOLTIP_GROUP_WORKFLOW => translate('Жизненный цикл'),
                TOOLTIP_GROUP_TRACE => translate('Трассировки')
            )
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
				),
                $object->getAttributesByGroup('skip-tooltip'),
                $object instanceof \PMWikiPage
                    ? array('Content')
                    : array()
			);

            if ( $object_it->get('VPD') == getSession()->getProjectIt()->get('VPD') ) {
                $tooltip_attributes = array_diff(
                    $tooltip_attributes,
                    array (
                        'Project'
                    )
                );
            }
		}
		else {
			$tooltip_attributes = array_merge(
				array('Caption', 'Description'),
				$tooltip_attributes
			);
		}
	    $system_attributes = $object->getAttributesByGroup('system');
 		$groupIndexes = array(
 		    '' => 0,
            'deadlines' => 1,
 		    'additional' => TOOLTIP_GROUP_ADDITIONAL,
            'workflow' => TOOLTIP_GROUP_WORKFLOW,
            'trace' => TOOLTIP_GROUP_TRACE
        );

 		foreach ( $object->getAttributes() as $attribute => $parms )
 	 	{
 	 		if ( $attribute == 'State' ) continue;
			if ( in_array($attribute, $system_attributes) ) continue;
			if ( $object_it->get($attribute) == '' ) continue;

 	 		$type = $object->getAttributeType($attribute);
 	 		if ( $type == '' ) continue;
 	 		if ( !in_array($attribute, $tooltip_attributes) ) continue;

            if ( $object->IsReference($attribute) ) {
                if ( !getFactory()->getAccessPolicy()->can_read($object->getAttributeObject($attribute)) ) continue;
            }

            $group = array_shift(
                array_intersect(
                    array_keys($groupIndexes), $object->getAttributeGroups($attribute)
                )
            );

            $data[] = array (
                'name' => $attribute,
                'title' => translate($object->getAttributeUserName($attribute)),
                'type' => $type,
                'text' => $this->getAttributeValue( $object_it, $attribute, $type ),
                'group' => $groupIndexes[$group]
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
			    return $object_it->get($attribute);

 			case 'wysiwyg':
			    return $object_it->getHtmlDecoded($attribute);

 			case 'date':
            case 'datetime':
			    return $object_it->getDateFormatShort($attribute);

            case 'float':
                if ( in_array('hours', $object_it->object->getAttributeGroups($attribute)) ) {
                    return getSession()->getLanguage()->getHoursWording($object_it->get($attribute));
                }
                elseif ( in_array('astronomic-time', $object_it->object->getAttributeGroups($attribute)) ) {
                    return getSession()->getLanguage()->getDurationWording($object_it->get($attribute), 24);
                }
                elseif ( in_array('working-time', $object_it->object->getAttributeGroups($attribute)) ) {
                    return getSession()->getLanguage()->getDurationWording($object_it->get($attribute), 8);
                }
                else {
                    return number_format(floatval($object_it->get($attribute)), 2, ',', ' ');
                }

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
                        while( !$ref_it->end() ) {
                            $titles[] = $uid->getUidWithCaption($ref_it);
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