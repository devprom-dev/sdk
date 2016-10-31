<?php

include_once SERVER_ROOT_PATH."pm/methods/c_state_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/ReorderWebMethod.php";

class PMPageList extends PageList
{
	private $order_method = null;
	private $reference_widgets = array();
	private $tags_url = '';
	
    function PMPageList( $object )
    {
        parent::PageList($object);
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

		$report = getFactory()->getObject('PMReport');
		$report_it = $report->getAll();
		$module = getFactory()->getObject('Module');
		$module_it = $module->getAll();

		$it = getFactory()->getObject('ObjectsListWidget')->getAll();
		while( !$it->end() )
		{
			switch( $it->get('ReferenceName') ) {
				case 'PMReport':
					$widget_it = $report_it->moveToId($it->getId());
					break;
				case 'Module':
					$widget_it = $module_it->moveToId($it->getId());
					break;
				default:
					$it->moveNext();
					continue;
			}
			$this->reference_widgets[$it->get('Caption')] = $widget_it->getUrl();
			$it->moveNext();
		}

		$this->tags_url = 'javascript:filterLocation.setup(\'tag=%\',1)';
	}
    
    function retrieve()
    {
   		$values = $this->getFilterValues();
		
		if ( !in_array($values['baseline'], array('', 'all', 'none')) )
		{
		    $this->getObject()->addPersister( new SnapshotItemValuePersister($values['baseline']) );
		}
		
    	return parent::retrieve();
    }
    
	function drawCell( $object_it, $attr )
    {
    	global $model_factory;
    	
        switch ( $attr )
        {
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
					echo '<span>';
					parent::drawCell( $object_it, $attr );
					echo '</span>';
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

			case 'Fact':
				echo getSession()->getLanguage()->getDurationWording($object_it->get($attr), 8);
				break;

			case 'StateDuration':
			case 'LeadTime':
				echo getSession()->getLanguage()->getDurationWording($object_it->get($attr));
				break;

            default:
                parent::drawCell( $object_it, $attr );
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
                        echo '<span class="tracing-ref">';
                        while( !$entity_it->end() ) {
                            $row_it = $entity_it->copy();
                            if ( $row_it->get('BrokenTraces') != "" ) {
                                echo $this->getTable()->getView()->render('pm/WikiPageBrokenIcon.php',
                                    array (
                                        'id' => $row_it->getId(),
                                        'url' => getSession()->getApplicationUrl($row_it)
                                    )
                                );
                            }
                            parent::drawRefCell( $row_it, $object_it, $attr );
                            $entity_it->moveNext();
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

	function getReferencesListWidget( $object )
	{
		foreach( $this->reference_widgets as $key => $widget ) {
			if ( is_a($object, $widget) ) return $widget;
		}
		return $this->reference_widgets[get_class($object)];
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
		$this->buildMethods();
		return parent::getRenderParms();
	}

	function buildFilterActions( & $base_actions )
	{
		parent::buildFilterActions( $base_actions );
		$this->buildFilterColumnsGroup( $base_actions, 'workflow' );
		$this->buildFilterColumnsGroup( $base_actions, 'trace' );
		$this->buildFilterColumnsGroup( $base_actions, 'time' );
		$this->buildFilterColumnsGroup( $base_actions, 'dates' );
	}
}