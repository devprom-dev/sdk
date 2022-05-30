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
				array('Caption', 'Description', 'State'),
				$tooltip_attributes
			);
		}
	    $system_attributes = array_merge(
	        $object->getAttributesByGroup('system'),
            $object->getAttributesByType('password'),
            array(
                'Password', 'RecentComment'
            )
        );

        $groupIndexes = array(
 		    '' => 0,
            'deadlines' => 1,
 		    'additional' => TOOLTIP_GROUP_ADDITIONAL,
            'workflow' => TOOLTIP_GROUP_WORKFLOW,
            'trace' => TOOLTIP_GROUP_TRACE
        );

 		foreach ( $object->getAttributes() as $attribute => $parms )
 	 	{
			if ( in_array($attribute, $system_attributes) ) continue;
			if ( $object_it->get($attribute) == '' ) continue;

 	 		$type = $object->getAttributeType($attribute);
 	 		if ( $type == '' ) continue;
 	 		if ( !in_array($attribute, $tooltip_attributes) ) continue;

 	 		$title = translate($object->getAttributeUserName($attribute));
            if ( $object->IsReference($attribute) ) {
                if ( !getFactory()->getAccessPolicy()->can_read($object->getAttributeObject($attribute)) ) continue;
                $refIt = $object_it->getRef($attribute);
                $typeAttribute = array_shift($refIt->object->getAttributesByGroup('type'));
                $title = $refIt->count() == 1 && $refIt->get($typeAttribute) != ''
                    ? $refIt->getRef($typeAttribute)->getDisplayName()
                    : $title;
            }

            $group = array_shift(
                array_intersect(
                    array_keys($groupIndexes), $object->getAttributeGroups($attribute)
                )
            );

            $data[] = array (
                'name' => $attribute,
                'title' => $title,
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
 	            if ( $object_it instanceof \StatableIterator )
 	        	    return $object_it->getStateIt()->get('Caption');
 	            else
 	                break;
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
			    return $object_it->getDateFormattedShort($attribute);

            case 'float':
                return getSession()->getLanguage()->formatFloatValue(
                    $object_it->get($attribute),
                    $object_it->object->getAttributeGroups($attribute)
                );

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
		 		    if ( $attribute == 'Caption' ) {
		 		        if ( count($object_it->object->getAttributesByGroup('hierarchy-parent')) > 0 ) {
		 		            if ( $object_it->get('ParentPath') != '' ) {
                                $parentTitles = array();
                                $parentIt = $object_it->object->getRegistry()->Query(
                                    array(
                                        new \FilterInPredicate(\TextUtils::parseIds($object_it->get('ParentPath'))),
                                        new \SortIndexClause()
                                    )
                                );
                                while( !$parentIt->end() ) {
                                    $parentTitles[] = $parentIt->getHtml($attribute);
                                    $parentIt->moveNext();
                                }
                                return join(' / ', $parentTitles);
                            }
                        }
                    }
 					return $object_it->getHtml($attribute);
		 		}
 	 	}
 	}	
}