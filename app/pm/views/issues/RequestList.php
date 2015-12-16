<?php

include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_priority_methods.php";
include_once SERVER_ROOT_PATH."pm/views/time/FieldSpentTimeRequest.php";
include_once SERVER_ROOT_PATH."pm/views/issues/FieldIssueEstimation.php";
include_once SERVER_ROOT_PATH."core/views/c_issue_type_view.php";
include_once SERVER_ROOT_PATH."core/views/c_priority_view.php";

class RequestList extends PMPageList
{
	private $estimation_field = null;
	private $visible_columns = array();
	private $priority_method = null;
	private $type_it = null;
	
	function RequestList( $object ) 
	{
		$object->setAttributeOrderNum('OrderNum', 35);
		
		parent::PMPageList($object);
	}
	
	function buildRelatedDataCache()
	{
		$this->priority_frame = new PriorityFrame();
		
		// cache priority method
		$has_access = getFactory()->getAccessPolicy()->can_modify($this->getObject())
				&& getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'Priority');
		
		if ( $has_access )
		{
			$this->priority_method = new ChangePriorityWebMethod( getFactory()->getObject('Priority')->getAll() );
		}

		$this->estimation_field = new FieldIssueEstimation();

		$this->type_it = getFactory()->getObject('RequestType')->getAll();
	}
	
	function setupColumns()
	{
		parent::setupColumns();
	
		$this->object->setAttributeVisible( 'Deadlines', false );
		
		$values = $this->getFilterValues();
		
		if ( !in_array($values['baseline'], array('all','hide','')) )
		{
			$this->object->setAttributeVisible( 'History', true );
		}
	}
	
 	function IsNeedToSelect()
	{
		return true;
	}
	
 	function IsNeedToSelectRow( $object_it )
	{
		return true;
	}
	
	function IsNeedToDisplayLinks( ) 
	{ 
	    return false; 
	}

	function getGroupFields() 
	{
		$fields = array_merge( parent::getGroupFields(), array( 'Tags', 'DeadlinesDate', 'DueDays') );
		return array_merge( $fields, array( 'ClosedInVersion', 'SubmittedVersion' ) );
	}

	function getGroup() 
	{
		$group = parent::getGroup();
		if ( $group == 'OwnerUser' ) return 'Owner';
		if ( $group == 'Type' ) return 'TypeBase';
		return $group;
	}
	
	function getRowBackgroundColor( $object_it ) 
	{
		return 'white';
	}

	function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			case 'Function':
				$title = $this->getTable()->getFeatureTitle($this->getGroupIt(), $object_it, $this->getUIDService());
				if ( $title == '' ) {
					parent::drawGroup($group_field, $object_it);
				}
				else {
					echo $title;
				}
				break;
			case 'Owner':
				$workload = $this->getTable()->getAssigneeWorkload();
				if ( count($workload) > 0 )
				{
					echo $this->getTable()->getView()->render('pm/UserWorkload.php', array (
							'user' => $object_it->getRef('Owner')->getDisplayName(),
							'data' => $workload[$object_it->get($group_field)]
					));
				}
				break;
			default:
				parent::drawGroup($group_field, $object_it);				
		}

		$this->getTable()->drawGroup($group_field, $object_it);
	}
	
	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
		switch ( $attr )
		{
			case 'Spent':
			    
			    $field = new FieldSpentTimeRequest( $object_it );

				$field->setEditMode( false );
			    
				$field->render( $this->getTable()->getView() );
                
			    break;

			case 'Links':
				
				if ( $object_it->get('LinksWithTypes') == '' ) break;

				foreach(preg_split('/,/', $object_it->get('LinksWithTypes')) as $type)
				{
					list( $type_name, $id ) = preg_split('/\:/', $type);
					
					$types_ids[$id] = $type_name; 
				}
				
				$items = array();
                while ( !$entity_it->end() )
                {
					$items[] = translate($types_ids[$entity_it->getId()]).
									': '.$this->getUidService()->getUidIconGlobal($entity_it, true);
				
					$entity_it->moveNext();
				}
                		
                echo join($items, ', ');
				
				break;
				
			case 'Priority':
				
				if ( is_object($this->priority_method) )
				{
					$this->priority_method->drawMethod( $object_it, 'Priority' );
				}
				else
				{
					parent::drawRefCell( $entity_it, $object_it, $attr );
				}
				
				break;
				
			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}

	function drawCell( $object_it, $attr )
	{
		global $model_factory;
		
		switch ( $attr )
		{
			case 'Caption':
    		    if ( !$this->visible_columns['Type'] && $this->type_it->count() > 0 )
    		    {
    		    	$this->type_it->moveToId($object_it->get('Type'));
    		    	echo '<img src="/images/'.IssueTypeFrame::getIcon($this->type_it).'">&nbsp;';
    		    	echo $object_it->getDisplayName();
    		    }
    		    else
    		    {
    		    	echo $object_it->get('Caption');
    		    }
			break;
			
			case 'Estimation':
				$this->estimation_field->setObjectIt($object_it);
				$this->estimation_field->draw($this->getTable()->getView());
    			break;
    			
			default:
			    parent::drawCell( $object_it, $attr );
		}
	}

	function getColumnWidth( $attr ) 
	{
		if ( $attr == 'Author' )
			return 80;
			
		if ( $attr == 'Owner' )
			return 80;
			
		if ( $attr == 'Priority' )
			return 80;

		if ( $attr == 'Submitted' )
			return 80;

		if ( $attr == 'Estimation' )
			return 80;
			
		if ( $attr == 'OrderNum' )
			return 70;

		return parent::getColumnWidth( $attr );
	}

	function getColumnAlignment( $attr ) 
	{
	    switch( $attr ) 
	    {
	        case 'Estimation': return 'center';
	            
	        case 'Priority': return 'center';

	        case 'Fact': return 'right';
	        
	        default: return parent::getColumnAlignment($attr); 
	    }
	}

	function getUrl() 
	{
		global $_SERVER;
		
		$parts = preg_split('/\&/', $_SERVER['QUERY_STRING']);
		
		foreach ( array_keys($parts) as $key )
		{ 
			if ( strpos($parts[$key], 'project=') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'offset') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'namespace=') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'module=') !== false )
			{
				unset($parts[$key]);
			}
		}
		
		return '?'.join($parts, '&');
	}
	
	function getPriorityIcon( $object_it )
	{
		return $this->priority_frame->getIcon( $object_it->get('Priority') );
	}
	
	function getColumnFields()
	{
		$cols = parent::getColumnFields();
		
		$cols[] = 'OrderNum';
		
		foreach ( $cols as $key => $col )
		{
			if ( $col == 'UID' || $col == 'Transition' || $col == 'Spent' )
			{
				continue;
			}

			if ( $this->object->getAttributeDbType($col) == '' )
			{
				unset( $cols[$key] );
			}
			
			if ( $col == 'Function' && !getSession()->getProjectIt()->getMethodologyIt()->HasFeatures() )
			{
				unset( $cols[$key] );
			}

			if ( $col == 'EstimationLeft' )
			{
				unset( $cols[$key] );
			}
		}
		
		unset( $cols['Deadlines'] );

		return $cols;
	}
	
 	function getNoItemsMessage()
	{
	    $module_it = getFactory()->getObject('Module')->getExact('issues-import');
	    
	    if ( getFactory()->getAccessPolicy()->can_read($module_it) )
	    {
	        $item = $module_it->buildMenuItem('?view=import&mode=xml&object=request');
	        
    		return str_replace('%1', $item['url'], text(1312));
	    }
	    
	    return parent::getNoItemsMessage();
	}
	
	function buildFilterActions( & $base_actions )
	{
	    parent::buildFilterActions( $base_actions );

	    $this->buildFilterColumnsGroup( $base_actions, 'workflow' );
	    
	    $this->buildFilterColumnsGroup( $base_actions, 'trace' );
	    
	    $this->buildFilterColumnsGroup( $base_actions, 'time' ); 
	    
	    $this->buildFilterColumnsGroup( $base_actions, 'dates' ); 
	}
	
	function getRenderParms()
	{
		$this->buildRelatedDataCache();
		
		$parms = parent::getRenderParms();
		
		$this->visible_columns = array (
				'Type' => $this->getColumnVisibility('Type')
		);
				
		return $parms; 
	}
}