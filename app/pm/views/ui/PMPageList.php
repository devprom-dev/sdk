<?php

include_once SERVER_ROOT_PATH."pm/methods/c_state_methods.php";

class PMPageList extends PageList
{
	private $state_names = array();
	
    function PMPageList( $object )
    {
        parent::PageList($object);
    }
    
    function retrieve()
    {
   		$values = $this->getFilterValues();
		
		if ( !in_array($values['baseline'], array('', 'all', 'none')) )
		{
		    $this->getObject()->addPersister( new SnapshotItemValuePersister($values['baseline']) );
		}
		
		if ( is_a($this->getObject(), 'MetaobjectStatable') && $this->getObject()->getStateClassName() != '' )
		{
			$state_it = $this->getObject()->cacheStates();
			
			while( !$state_it->end() )
			{
				$this->state_names[$state_it->get('ReferenceName')] = $state_it->getDisplayName();
				
				$state_it->moveNext();
			}
		}
			
		
    	return parent::retrieve();
    }
    
	function drawCell( $object_it, $attr )
    {
    	global $model_factory;
    	
        switch ( $attr )
        {
            case 'State':
    
            	echo $this->state_names[$object_it->get('State')];
    
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
    
 	function setupColumns()
	{
	   	$values = $this->getFilterValues();
		
		if ( !in_array($values['baseline'], array('', 'all', 'none')) )
		{
		    $this->getObject()->addAttribute( 'History', 'TEXT', translate('История изменений'), true );
		}
		
	    parent::setupColumns();
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
}