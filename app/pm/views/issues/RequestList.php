<?php

include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/views/time/FieldSpentTimeRequest.php";
include_once SERVER_ROOT_PATH."pm/views/issues/FieldIssueEstimation.php";
include_once SERVER_ROOT_PATH."core/views/c_issue_type_view.php";

class RequestList extends PMPageList
{
	private $estimation_field = null;
	private $visible_columns = array();
	private $type_it = null;
    private $strategy = null;
    private $velocity = 0;
    private $assigneeField = null;

	function RequestList( $object ) 
	{
		$object->setAttributeOrderNum('OrderNum', 35);
		
		parent::PMPageList($object);
	}
	
	function buildRelatedDataCache()
	{
        $this->non_terminal_states = $this->getObject()->getNonTerminalStates();
		$this->estimation_field = new FieldIssueEstimation();
		$this->type_it = getFactory()->getObject('RequestType')->getAll();
        $this->strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
        $this->velocity = getSession()->getProjectIt()->getVelocityDevider();

        if ( getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'Owner') ) {
            $this->assigneeField = new FieldReferenceAttribute($this->getObject()->getEmptyIterator(), 'Owner');
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
		return array_merge(
            parent::getGroupFields(),
            array(
                'Tags',
                'ClosedInVersion',
                'SubmittedVersion',
                'Links'
            )
        );
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
					echo $this->getRenderView()->render('pm/UserWorkload.php', array (
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
                $field->setShortMode();
				$field->setEditMode( false );
				$field->render( $this->getRenderView() );
			    break;

			case 'Links':
				if ( $object_it->get('LinksWithTypes') == '' ) break;

				foreach(preg_split('/,/', $object_it->get('LinksWithTypes')) as $type) {
					list( $type_name, $id ) = preg_split('/\:/', $type);
					$types_ids[$id] = $type_name;
				}
				
				$items = array();
                while ( !$entity_it->end() )
                {
                    $it = $entity_it->getSpecifiedIt();
                    $text = $this->getUidService()->getUidIconGlobal($it, true);
                    if ( !$this instanceof PageBoard ) {
                        $text .= '<span class="ref-name">'.$it->getDisplayNameExt().'</span>';
                    }
					$items[] = translate($types_ids[$it->getId()]).': '.$text;
					$entity_it->moveNext();
				}
                		
                echo join($items, '<div/> ');
				break;
				
            case 'Author':
                if ( $entity_it->get('CustomerId') > 0 ) {
                    parent::drawRefCell(
                        getFactory()->getObject($entity_it->get('CustomerClass'))->getExact($entity_it->get('CustomerId')),
                        $object_it,
                        $attr);
                }
                else {
                    echo $entity_it->getDisplayName();
                }
                break;

            case 'Owner':
                if ( is_object($this->assigneeField) ) {
                    $this->assigneeField->setObjectIt($object_it);
                    $this->assigneeField->draw($this->getRenderView());
                }
                else {
                    parent::drawRefCell( $entity_it, $object_it, $attr );
                }
                break;

			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Caption':
    		    if ( !$this->visible_columns['Type'] && $this->type_it->count() > 0 )
    		    {
    		    	$this->type_it->moveToId($object_it->get('Type'));
					echo '<img src="/images/'.IssueTypeFrame::getIcon($this->type_it).'">&nbsp;';
					if ( $this->type_it->getId() != '' ) {
						echo $this->type_it->getDisplayName().': ';
					}
    		    }
                parent::drawCell($object_it, $attr);
			break;
			
			case 'Estimation':
                if ( in_array('astronomic-time', $this->getObject()->getAttributeGroups($attr)) ) {
                    parent::drawCell($object_it, $attr);
                }
                else {
                    echo '<div style="margin-left:22px;">';
                    $this->estimation_field->setObjectIt($object_it);
                    $this->estimation_field->draw($this->getRenderView());
                    echo '</div>';
                }
    			break;

            case 'DeliveryDate':
                $deadline_alert =
                    in_array($object_it->get('State'), $this->non_terminal_states)
                    && $object_it->get('DueWeeks') < 4 && $object_it->get('DeliveryDate') != '';

                if ( $deadline_alert ) {
                    echo '<span class="date-label label '.($object_it->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'">';
                    parent::drawCell($object_it, $attr);
                    echo '</span>';
                } else {
                    parent::drawCell($object_it, $attr);
                }
                break;

			default:
			    parent::drawCell( $object_it, $attr );
		}
	}

	function drawTotal($object_it, $attr)
    {
        switch( $attr ) {
            case 'Estimation':
                echo $this->strategy->getDimensionText($object_it->get($attr));
                if ( $this->velocity > 0 ) {
                    echo ' ('.str_replace('%1',round($object_it->get($attr) / $this->velocity, 1),text(2283)).')';
                }
                break;
            default:
                parent::drawTotal($object_it, $attr);
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
		
		return $cols;
	}
	
 	function getNoItemsMessage()
	{
	    $module_it = getFactory()->getObject('Module')->getExact('issues-import');
	    
	    if ( getFactory()->getAccessPolicy()->can_read($module_it) && !getSession()->getProjectIt()->IsPortfolio() )
	    {
	        $item = $module_it->buildMenuItem('?view=import&mode=xml&object=request');
	        
    		return str_replace('%1', $item['url'], text(1312));
	    }
	    
	    return parent::getNoItemsMessage();
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