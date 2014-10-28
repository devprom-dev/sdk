<?php

include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_priority_methods.php";
include_once SERVER_ROOT_PATH."pm/views/time/FieldSpentTimeRequest.php";
include_once SERVER_ROOT_PATH."core/views/c_issue_type_view.php";
include_once SERVER_ROOT_PATH."core/views/c_priority_view.php";

class RequestList extends PMPageList
{
	private $estimation_actions = array();
	
	private $visible_columns = array();
	
	function RequestList( $object ) 
	{
		$this->priority_frame = new PriorityFrame();
		
		parent::PMPageList($object);
	}
	
	function buildRelatedDataCache()
	{
		// cache priority method
		$this->priority_method = new ChangePriorityWebMethod( getFactory()->getObject('Priority')->getAll() );
		
		$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		
		foreach( $strategy->getScale() as $item )
		{
			$method = new ModifyAttributeWebMethod($object_it, 'Estimation', $item);
				
			$method->setCallback( "donothing" );
				
			$this->estimation_actions[$item] = array( 
				    'name' => $item,
					'method' => $method 
			);
		}
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
		$fields = array_merge( parent::getGroupFields(), array( 'Tags', 'DeadlinesDate' ) );
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasVersions() )
		{
			return array_merge( $fields,
				array( 'ClosedInVersion', 'SubmittedVersion' ) );
		}
		else
		{
			return $fields;
		}
	}

	function getRowBackgroundColor( $object_it ) 
	{
		return 'white';
	}

	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
		switch ( $attr )
		{
			case 'Author':
				
				if ( $object_it->get('ExternalAuthor') != '' )
				{
					echo $object_it->get('ExternalAuthor');
					return;
				}
				
				parent::drawRefCell( $entity_it, $object_it, $attr );
				
				break;
				
			case 'Spent':
			    
			    $field = new FieldSpentTimeRequest( $object_it );

				$field->setEditMode( false );
			    
				$field->render( $this->getTable()->getView() );
                
			    break;

			case 'Links':
				
				if ( $object_it->get('LinksWithTypes') == '' ) break;

				foreach(preg_split('/,/', $object_it->get('LinksWithTypes')) as $type)
				{
					list( $type, $id ) = preg_split('/\:/', $type);
					
					$types_ids[$id] = $type; 
				}
				
                while ( !$entity_it->end() )
                {
					$items[] = translate($types_ids[$id]).': '.$this->getUidService()->getUidIconGlobal($entity_it, true);
				
					$entity_it->moveNext();
				}
                		
                echo join($items, ', ');
				
				break;
				
			case 'Priority':
				
				$this->priority_method->drawMethod( $object_it, 'Priority' );
				
				break;
				
			case 'Function':
				if( $object_it->get($attr) == '' )
				{
					echo text(888);
				}
				else
				{
					$uid = new ObjectUid;
					
					$function_it = $object_it->getRef($attr);
					$uid->drawUidInCaption($function_it);
					
					if ( $function_it->get('Importance') > 0 )
					{
						$importance_it = $function_it->getRef('Importance');
						echo ' ('.$importance_it->getDisplayName().')';
					}
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
    
    			$type_it = $object_it->getRef('Type');
    			
    			echo '<img src="/images/'.IssueTypeFrame::getIcon($type_it).'">&nbsp;';
    		    
    		    if ( !$this->visible_columns['Type'] ) echo $object_it->getTypeName().': ';
    			
    			echo $object_it->get('Caption');
    			
			break;
			
			case 'Estimation':
			    
				$actions = $this->estimation_actions;
				
				foreach( $actions as $key => $action )
				{
					$method = $action['method'];
					
					$actions[$key]['url'] = $method->getJSCall(array(), $object_it);
				}
				
				echo $this->getTable()->getView()->render('pm/EstimationIcon.php', array (
						'data' => $object_it->get('Estimation') != '' ? $object_it->get('Estimation') : '0',
						'items' => $actions
				));
			    
    			break;
    			
			case 'OrderNum':

			    if ( getFactory()->getAccessPolicy()->can_modify($object_it) )
			    {
        			$method = new AutoSaveFieldWebMethod( $object_it, 'OrderNum' );
        			
        			$method->setInput();
        			
        			$method->draw();
			    }
			    
			    break;
			    

			case 'RecentComment':
				
				parent::drawCell( $object_it, $attr );
				
				echo $this->getTable()->getView()->render('core/CommentsIcon.php', array (
						'object_it' => $object_it,
						'redirect' => 'donothing'
				));

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
			return '50';

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
			if ( $col == 'UID' || $col == 'Transition' || $col == 'TransitionComment' || $col == 'Spent' )
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
				'Type' => $this->getColumnVisibility( 'Type' )
		);
				
		return $parms; 
	}
}