<?php

include_once SERVER_ROOT_PATH."pm/methods/c_state_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/ReorderWebMethod.php";

class PMPageList extends PageList
{
	private $order_method = null;
	private $reference_widgets = array();
	
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
		
		$it = getFactory()->getObject('ObjectsListWidget')->getAll();
		while( !$it->end() )
		{
			$menu = getFactory()->getObject($it->get('ReferenceName'))->getExact($it->getId())->buildMenuItem();
			$this->reference_widgets[$it->get('Caption')] = $menu['url'];
			$it->moveNext();
		}
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
    
			case 'History':
			    
				$log = $model_factory->getObject('ChangeLog');
			
        		$log->addFilter( new ChangeLogItemFilter($object_it->getCurrentIt()) );
        		
        		$log->addFilter( new ChangeLogStartFilter($object_it->get($attr)) );
        		
        		$log_it = $log->getLatest(1);
        		
        		if ( $log_it->count() > 0 )
        		{
        		    echo '<div>';
       				    drawMore($log_it, 'Content');
       				echo '</div>';
        
        			echo '<br/>';

        			$report = $model_factory->getObject('PMReport');
        			$report_it = $report->getExact( 'project-log' );
        			
        			$class = strtolower(get_class($this->getObject()));
        			
    				echo '<a href="'.$report_it->getUrl().'&object='.$class.'&'.$class.'='.$object_it->getId().'&start='.$object_it->get($attr).'">';
    				    echo translate('Все изменения');
    				echo '</a>';
        		}
			    
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
				echo $this->getTable()->getView()->render('core/CommentsIcon.php', array (
						'object_it' => $object_it,
						'redirect' => 'donothing'
				));
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
                
            default:
                
                parent::drawRefCell( $entity_it, $object_it, $attr );
        }
    }

	function getReferencesListWidget( $object )
	{
		return $this->reference_widgets[get_class($object)];
	}
    
 	function setupColumns()
	{
	   	$values = $this->getFilterValues();
		
		if ( !in_array($values['baseline'], array('', 'all', 'none')) )
		{
		    $this->getObject()->addAttribute( 'History', 'TEXT', translate('История изменений'), true );
		}
		
	    parent::setupColumns();
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
		        $ids = getSession()->getProjectIt()->getRef('LinkedProject')->fieldToArray('pm_ProjectId');
		        
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
}