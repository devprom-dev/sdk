<?php
use Devprom\ProjectBundle\Service\Model\ModelService;
use Devprom\ProjectBundle\Service\Widget\WidgetService;
include_once SERVER_ROOT_PATH . "pm/methods/TransitionStateMethod.php";
include "FieldPriority.php";

class PMPageList extends PageList
{
	private $tags_url = '';
    private $visibleColumnsCache = array();
    private $priorityField = null;
	
    function PMPageList( $object )
    {
        parent::PageList($object);
    }

    function extendModel()
    {
        parent::extendModel();

        if ( getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'Priority') ) {
            $this->priorityField = new FieldPriority($this->getObject()->getEmptyIterator());
        }
    }

    function combineCaptionWithDescription() {
        return true;
    }

    function buildMethods()
	{
		$this->tags_url = 'javascript:filterLocation.setup(\'tag=%\',1)';
	}

	function checkColumnHidden( $attr ) {
        return array_key_exists($attr, $this->visibleColumnsCache) && !$this->visibleColumnsCache[$attr];
    }

    function getColumnVisibility($attr) {
        if ( $this->combineCaptionWithDescription() && $attr == 'Description' && array_key_exists($attr, $this->visibleColumnsCache) ) {
            return false;
        }
        return parent::getColumnVisibility($attr);
    }

    function getHeaderAttributes( $attribute )
    {
        switch( $attribute ) {
            case 'Caption':
                $parms = parent::getHeaderAttributes($attribute);
                if ( $this->combineCaptionWithDescription() && $this->visibleColumnsCache['Description'] ) {
                    $parms['name'] = str_replace('%1', $parms['name'], text(2305));
                }
                return $parms;
            default:
                return parent::getHeaderAttributes($attribute);
        }
    }

	function drawCell( $object_it, $attr )
    {
        switch ( $attr )
        {
            case 'Caption':
                parent::drawCell($object_it, $attr);
                if ( $this->checkColumnHidden('Tags') && $object_it->get('Tags') != '' ) {
                    echo ' ';
                    $this->drawRefCell($this->getFilteredReferenceIt('Tags', $object_it->get('Tags')), $object_it, 'Tags');
                }
                $this->drawCell($object_it, 'DescriptionWithInCaption');
                break;

            case 'DescriptionWithInCaption':
                if ( $this->visibleColumnsCache['Description'] && trim($object_it->get('Description')," \r\n") != '' ) {
                    echo '<div style="margin-top: 4px">';
                        if ( $object_it->object->getAttributeType('Description') == 'wysiwyg' ) {
                            $field = new FieldWYSIWYG();
                            $field->setValue($object_it->get('Description'));
                            $field->setObjectIt($object_it);
                            $field->drawReadonly();
                        }
                        else {
                            parent::drawCell($object_it, 'Description');
                        }
                    echo '</div>';
                }
                break;

            case 'State':
            	echo $this->getRenderView()->render('pm/StateColumn.php', array (
                    'stateIt' => $object_it->getStateIt()
                ));
                break;
    
			case 'RecentComment':
				if ( $object_it->get($attr) != '' ) {
					echo '<div class="recent-comments">';
					if ( $object_it->get('RecentCommentAuthor') != '' ) {
						echo $this->getRenderView()->render('core/UserPictureMini.php', array (
							'id' => $object_it->get('RecentCommentAuthor'),
							'image' => 'userpics-mini',
							'class' => 'user-mini',
                            'date' => $object_it->getDateFormattedShort('RecentCommentDate')
						));
					}
					echo '<span class="reset wysiwyg" style="margin-top: 4px;">';
                        $field = new FieldWYSIWYG();
                        $field->setValue($object_it->get($attr));
                        $field->setObjectIt($object_it);
                        $field->drawReadonly();
					echo '</span>';
                    echo '<div class="clearfix"></div>';
					echo '</div>';
				}
				else {
					$text = translate('Добавить');
				}
				echo $this->getRenderView()->render('core/CommentsIcon.php', array (
                    'object_it' => $object_it,
                    'redirect' => 'donothing',
                    'text' => $text
				));
				break;

            default:
                if ( $attr != 'UID' && in_array('computed', $object_it->object->getAttributeGroups($attr)) ) {
                    $lines = array();
                    $times = 0;
                    $result = ModelService::computeFormula($object_it, $object_it->object->getDefaultAttributeValue($attr));
                    foreach( $result as $computedItem ) {
                        if ( is_object($computedItem) ) {
                            if ( $times > 0 ) {
                                echo '<br/>';
                            }
                            parent::drawRefCell($computedItem, $object_it, $attr);
                            $times++;
                        }
                        else {
                            $lines[] = $computedItem;
                        }
                    }
                    if ( count($lines) > 0 ) {
                        echo join('<br/>', $lines);
                    }
                    break;
                }

                switch ( $object_it->object->getAttributeType($attr) ) {
                    case 'wysiwyg':
                        if ( $object_it->get($attr) != '' ) {
                            $field = new FieldWYSIWYG();
                            $field->setValue($object_it->get($attr));
                            $field->setObjectIt($object_it);
                            $field->drawReadonly();
                        }
                        break;
                    default:
                        parent::drawCell( $object_it, $attr );
                }
        }
    }

	function drawRefCell( $entity_it, $object_it, $attr )
    {
        switch( $attr )
        {
			case 'Tags':
				$tagIds = $entity_it->idsToArray();
				foreach( $entity_it->fieldToArray('Caption') as $key => $name ) {
					$name = '<a href="'.preg_replace('/%/', $tagIds[$key], $this->tags_url).'">'.$name.'</a>';
					$html[] = '<span class="label label-info label-tag">'.$name.'</span>';
				}
				echo join(' ', $html);
				break;

            case 'Priority':
                if ( is_object($this->priorityField) ) {
                    $this->priorityField->setObjectIt($object_it);
                    $this->priorityField->draw($this->getRenderView());
                }
                else {
                    parent::drawRefCell( $entity_it, $object_it, $attr );
                }
                break;
                
            default:
                switch( $entity_it->object->getEntityRefName() )
                {
                    case 'WikiPage':
                        $ids = $entity_it->idsToArray();

                        $widget_it = $this->getTable()->getReferencesListWidget($entity_it, $attr);
                        if ( $widget_it->getId() != '' && count($ids) > 1 )
                        {
                            $url = WidgetUrlBuilder::Instance()->buildWidgetUrlIt($entity_it, 'ids', $widget_it);
                            $text = count($ids) > 10
                                        ? str_replace('%1', count($ids) - 10, text(2028))
                                        : text(2034);

                            $entity_it = $entity_it->object->createCachedIterator(
                                array_slice($entity_it->getRowset(),0,10)
                            );
                        }

                        $items = $this->getRefNames($entity_it, $object_it, $attr);
                        foreach( $items as $objectId => $value ) {
                            $entity_it->moveToId($objectId);
                            if ( $entity_it->get('Suspected') > 0 ) {
                                $items[$objectId] = WidgetService::getHtmlBrokenIcon(
                                    $entity_it->getId(), getSession()->getApplicationUrl($entity_it)) . $value;
                            }
                        }

                        echo '<span class="tracing-ref" entity="'.get_class($entity_it->object).'">';
                            echo '<span>'.join('</span> <span>',$items).'</span>';
                            if ( $url != '' ) {
                                echo ' <a class="dashed" target="_blank" href="'.$url.'">'.$text.'</a>';
                            }
                        echo '</span>';
                        break;
                    default:
                        parent::drawRefCell( $entity_it, $object_it, $attr );
                }
        }
    }

	function drawGroup($group_field, $object_it)
	{
		switch($group_field)
		{
			case 'Tags':
				$ref_it = $this->getGroupIt();
				foreach( preg_split('/,/', $object_it->get($group_field)) as $group_id ) {
					$ref_it->moveToId($group_id);
					$html[] = '<span class="label label-info">'.$ref_it->getDisplayName().'</span>';
				}
				echo join(' ', $html);
				break;

			default:
				parent::drawGroup($group_field, $object_it);
		}
	}

    function getGroupEntityName( $groupField, $object_it, $referenceIt )
    {
        if ( $referenceIt->object instanceof Request ) return "";
        if ( $referenceIt->object instanceof User ) return "";
        if ( $referenceIt->object instanceof Build ) {
            return $object_it->object->getAttributeUserName($groupField);
        }
        return parent::getGroupEntityName($groupField, $object_it, $referenceIt);
    }

	function getColumnWidth($attr)
    {
        switch( $attr ) {
            case 'RecentComment':
                return '25%';
            default:
                return parent::getColumnWidth($attr);
        }
    }

    function getGroupFields()
	{
	    $object = $this->getObject();

		$skip = array_filter($object->getAttributesByGroup('workflow'), function($value) {
			return $value != 'State';
		});

		$skip = array_merge($skip,
            array_filter($object->getAttributesByGroup('trace'), function($value) use ($object) {
                return $object->getAttributeOrigin($value) != ORIGIN_CUSTOM;
            })
        );

		return array_diff(parent::getGroupFields(), $skip );
	}

 	function getGroupDefault()
 	{
 		$default = parent::getGroupDefault();
 		
 		if ( $default == '' )
 		{
	 		$set = getFactory()->getObject('SharedObjectSet');
		    if ( $set->sharedInProject($this->getObject(), getSession()->getProjectIt()) )
		    {
		        $ids = getSession()->getLinkedIt()->idsToArray();
		        if ( count($ids) > 0 ) return 'Project';
		    }
 		}
	    
 	    return $default;
 	}
 	
	function getRenderParms()
	{
        $cache = array('Tags');
        if ( $this->getObject()->getAttributeType('Caption') != '' && parent::getColumnVisibility('Caption') ) {
            $cache[] = 'Description';
        }

        foreach( $cache as $column ) {
            if ( $this->getObject()->getAttributeType($column) == '' ) continue;
            $this->visibleColumnsCache[$column] = parent::getColumnVisibility($column);
        }

		$this->buildMethods();
		return parent::getRenderParms();
	}

    public function getRefNames($entity_it, $object_it, $attr )
    {
        if ( $entity_it instanceof VersionIterator ) {
            return parent::getRefNames($entity_it->getObjectIt(), $object_it, $attr );
        }
        return parent::getRefNames($entity_it, $object_it, $attr );
    }

    function IsNeedToDisplayOperations() {
        return $_REQUEST['dashboard'] == '';
    }

}