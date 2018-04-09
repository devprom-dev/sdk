<?php

use Devprom\ProjectBundle\Service\Model\ModelService;
include_once SERVER_ROOT_PATH."pm/methods/c_state_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/ReorderWebMethod.php";

class PMPageList extends PageList
{
	private $order_method = null;
	private $tags_url = '';
    private $visibleColumnsCache = array();
	
    function PMPageList( $object )
    {
        parent::PageList($object);
    }

    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        foreach( array_keys($object->getAttributes()) as $attr ) {
            if ( !$object->IsReference($attr) ) continue;
            if ( !getFactory()->getAccessPolicy()->can_read($this->getObject()->getAttributeObject($attr)) ) {
                $object->removeAttribute($attr);
            }
        }
    }

    function buildMethods()
	{
		// reorder method
		$has_access = getFactory()->getAccessPolicy()->can_modify($this->getObject())
				&& getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'OrderNum');
		
		if ( $has_access )
		{
			$this->order_method = new ReorderWebMethod($this->getObject()->getEmptyIterator());
			$this->order_method->setInput();
		}
		$this->tags_url = 'javascript:filterLocation.setup(\'tag=%\',1)';
	}

	function checkColumnHidden( $attr ) {
        return array_key_exists($attr, $this->visibleColumnsCache) && !$this->visibleColumnsCache[$attr];
    }

    function getColumnVisibility($attr) {
        if ( $attr == 'Description' && array_key_exists($attr, $this->visibleColumnsCache) ) {
            return false;
        }
        return parent::getColumnVisibility($attr);
    }

    function getHeaderAttributes( $attribute )
    {
        switch( $attribute ) {
            case 'Caption':
                $parms = parent::getHeaderAttributes($attribute);
                if ( $this->visibleColumnsCache['Description'] ) {
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
                echo '<div style="margin-top: 4px">';
                    $this->drawCell($object_it, 'DescriptionWithInCaption');
                echo '</div>';
                break;

            case 'DescriptionWithInCaption':
                if ( $this->visibleColumnsCache['Description'] && trim($object_it->get('Description')," \r\n") != '' ) {
                    $field = new FieldWYSIWYG();
                    $field->setValue($object_it->get('Description'));
                    $field->setObjectIt($object_it);
                    $field->drawReadonly();
                }
                break;

            case 'State':
            	echo $this->getTable()->getView()->render('pm/StateColumn.php', array (
                    'color' => $object_it->get('StateColor'),
                    'name' => $object_it->get('StateName'),
                    'terminal' => $object_it->get('StateTerminal') == 'Y'
                ));
                break;
    
			case 'OrderNum':
				if ( is_object($this->order_method) )
				{
					$this->order_method->setObjectIt($object_it);
        			$this->order_method->draw();
				}
				else
				{
					parent::drawCell( $object_it, $attr );
				}
			    
			    break;
			    
			case 'RecentComment':
				if ( $object_it->get($attr) != '' ) {
					echo '<div class="recent-comments">';
					if ( $object_it->get('RecentCommentAuthor') != '' ) {
						echo $this->getTable()->getView()->render('core/UserPictureMini.php', array (
							'id' => $object_it->get('RecentCommentAuthor'),
							'image' => 'userpics-mini',
							'class' => 'user-mini'
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
				echo $this->getTable()->getView()->render('core/CommentsIcon.php', array (
                    'object_it' => $object_it,
                    'redirect' => 'donothing',
                    'text' => $text
				));
				break;

            default:
                if ( in_array('computed', $this->object->getAttributeGroups($attr)) ) {
                    $lines = array();
                    $times = 0;
                    $result = ModelService::computeFormula($object_it, $this->object->getDefaultAttributeValue($attr));
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

                switch ( $this->object->getAttributeType($attr) ) {
                    case 'text':
                        echo $object_it->getHtmlValue($object_it->getHtmlDecoded($attr));
                        break;
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
            case 'Watchers':
                $user_it = $object_it->getRef($attr);
                $emails = $object_it->get('WatchersEmails') != ''
                        ? preg_split('/,/', $object_it->get('WatchersEmails')) : array();
                echo join(', ', array_merge($user_it->fieldToArray('Caption'), $emails));
                break;

			case 'Tags':
				$tagIds = $entity_it->idsToArray();
				foreach( $entity_it->fieldToArray('Caption') as $key => $name ) {
					$name = '<a href="'.preg_replace('/%/', $tagIds[$key], $this->tags_url).'">'.$name.'</a>';
					$html[] = '<span class="label label-info label-tag">'.$name.'</span>';
				}
				echo join(' ', $html);
				break;
                
            default:
                switch( $entity_it->object->getEntityRefName() )
                {
                    case 'WikiPage':
                        $ids = $entity_it->idsToArray();
                        $widget_it = $this->getTable()->getReferencesListWidget($entity_it, $attr);
                        if ( $widget_it->getId() != '' && count($ids) > 1 )
                        {
                            $url = $widget_it->getUrl('filter=skip&'.strtolower(get_class($entity_it->object)).'='.join(',',$ids));
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
                            if ( $entity_it->get('BrokenTraces') != "" ) {
                                $items[$objectId] = $this->getTable()->getView()->render('pm/WikiPageBrokenIcon.php',
                                        array (
                                            'id' => $entity_it->getId(),
                                            'url' => getSession()->getApplicationUrl($entity_it)
                                        )
                                    ).$value;
                            }
                        }

                        echo '<span class="tracing-ref" entity="'.get_class($entity_it->object).'">';
                            echo join(' ',$items);
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

	function getColumnFields()
	{
		return array_merge(parent::getColumnFields(), $this->getObject()->getAttributesByGroup('workflow'));
	}

	function getGroupFields()
	{
		$skip = array_filter($this->getObject()->getAttributesByGroup('workflow'), function($value) {
			return $value != 'State';
		});
		$skip = array_merge($skip, $this->getObject()->getAttributesByGroup('trace'));
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

	function buildFilterActions( & $base_actions )
	{
		parent::buildFilterActions( $base_actions );
		$this->buildFilterColumnsGroup( $base_actions, 'workflow' );
		$this->buildFilterColumnsGroup( $base_actions, 'trace' );
		$this->buildFilterColumnsGroup( $base_actions, 'workload' );
		$this->buildFilterColumnsGroup( $base_actions, 'dates' );
        $this->buildFilterColumnsGroup( $base_actions, 'sla' );
	}

    protected function getRefNames($entity_it, $object_it, $attr )
    {
        if ( $entity_it instanceof VersionIterator ) {
            return parent::getRefNames($entity_it->getObjectIt(), $object_it, $attr );
        }
        return parent::getRefNames($entity_it, $object_it, $attr );
    }
}