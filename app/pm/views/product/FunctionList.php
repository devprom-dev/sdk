<?php

if ( !class_exists('IssuesProgressFrame', false) )
{
    include SERVER_ROOT_PATH.'pm/views/c_request_frame.php';
}

class FunctionList extends PMPageList
{
 	var $request_agg_it, $strategy;
 	
 	private $request_non_terminal_states = array();
 	private $visible_columns = array();
 	private $trace_attributes = array();
 	private $group_defined = false;
 	
 	function __construct( $object )
 	{
 	    parent::__construct( $object );
 	}
 	
	function retrieve()
	{
		$this->group_defined =
            !in_array($this->getGroup(), array('','none'))
            || count($this->getIds()) > 0;
		
		parent::retrieve();

		// cache requests per feature
		$filters = $this->getFilterValues();
		
		$request = getFactory()->getObject('pm_ChangeRequest');
		$request->addFilter( new RequestStagePredicate($filters['stage']) );
		$request->addFilter( new StatePredicate('notresolved') );
		
		$this->strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		$this->request_agg_it = $request->getRequestsAggByFunction();
		$this->request_agg_it->buildPositionHash( array('Function') );
	}

	function getSorts()
	{
		$sorts = parent::getSorts();
		array_unshift($sorts, new SortFeatureHierarchyClause());
		return $sorts;
	}

	function getSortingParms()
	{
		return array (
				'SortIndex',
				'asc'
		);
	}
	
	function setupColumns()
	{
		$this->object->addAttribute('UncompletedIssues', '', text(1342), false, false, '', 40);
		
		parent::setupColumns();
		
		$values = $this->getFilterValues();
		
		$this->object->setAttributeVisible( 'UncompletedIssues', $values['state'] != 'closed' );
		$this->object->setAttributeVisible( 'Request', $values['state'] == 'closed' );
	}

	function getGroupFields()
	{
		$fields = parent::getGroupFields();
		unset($fields['ParentFeature']);
		return $fields;
	}

    function getGroupDefault() {
        return '';
    }

    function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			default:
				return parent::drawGroup( $group_field, $object_it );
		}
	} 
	
	function drawCell( $object_it, $attr )
	{
		global $model_factory;
		
		switch( $attr )
		{
			case 'CaptionShort':
				if ( !$this->group_defined ) {
					$arcs = array_filter(preg_split('/,/', $object_it->get('ParentPath')), function($value) { return $value != ''; });
					$offset = (count($arcs) - 1) * 25;
				} else {
					$offset = 0;
				}
				echo '<div style="padding-left:'.$offset.'px;">';
					parent::drawCell($object_it, 'Caption');
                    parent::drawCell($object_it, 'DescriptionWithInCaption');
				echo '</div>';
				break;

		    case 'Caption':
		    	if ( !$this->group_defined ) {
			    	$arcs = array_filter(preg_split('/,/', $object_it->get('ParentPath')), function($value) { return $value != ''; });
			    	$offset = (count($arcs) - 1) * 25;
		    	} else {
		    		$offset = 0;
		    	}
		    	echo '<div style="padding-left:'.$offset.'px;">';
   		    		echo $object_it->get($this->visible_columns['Type'] ? 'Caption' : 'CaptionAndType');
                    if ( $this->checkColumnHidden('Tags') && $object_it->get('Tags') != '' ) {
                        echo ' ';
                        //$this->drawRefCell($this->getFilteredReferenceIt('Tags', $object_it->get('Tags')), $object_it, 'Tags');
                    }
                    parent::drawCell($object_it, 'DescriptionWithInCaption');
   		    	echo '</div>';
    		    break;
		    	
		    case 'Description':
		    	drawMore($object_it, 'Description', 12);
		    	break;
		    	
		    case 'Progress':
	    		$filters = $this->getFilterValues();
	    		
				echo '<div style="padding:2px 8px 0 0;">';
					$frame = new IssuesProgressFrame( 
							getFactory()->getObject('Request')->getRegistry()->Query(
										array (
												new RequestStagePredicate($filters['stage']),
												new RequestFeatureFilter($object_it->getId())			
										)
								)->getProgress(), false 
					);
					$frame->draw();
				echo '</div>';
				break;
				
		    case 'UncompletedIssues':
				$this->request_agg_it->setStop( 'Function', $object_it->getId() );

				$session = getSession();
				
				$values = $this->getFilterValues();
				
				$report_it = getFactory()->getObject('PMReport')->getExact('allissues');
				
				if ( getFactory()->getAccessPolicy()->can_read($report_it) )
				{
				    $item = $report_it->buildMenuItem('&function='.$object_it->getId().'&state='.join(',',$this->request_non_terminal_states));
	
				    $url = $item['url'];
				
	    			if ( $values['view'] != '' )
	    	 		{
	    	 			$url .= '&view='.$values['view'];
	    	 		} 		
	    
	    			if ( $values['stage'] != '' )
	    	 		{
	    	 			$version = $model_factory->getObject('Stage');
	    	 			
	    	 			$version_it = $version->getExact( $values['stage'] );
	    	 			
	    	 			if ( $version_it->getId() != '' )
	    	 			{
	    	 				$url .= '&release='.$version_it->get('Version');
	    	 			}
	    	 		} 		
	    
	    			$frame = new IssuesGroupFrame( $this->request_agg_it, false );
	    			
	    			$frame->setUrl( $url );
	    			
	    			$frame->draw();
				}
				break;		    	
		    	
		    case 'Workload':
			    $value = $object_it->get('Workload') == '' ? 0 : $object_it->get('Workload');
			     
			    echo round($value, 1).' '.translate('дн.');
			    
			    if ( $value > 0 )
			    {
			        echo ' ('.$this->strategy->getDimensionText(round($object_it->get('EstimationLeft'),1)).')';
			    }
			    break;
			    
		    case 'Estimation':
			    $value = $object_it->get('Estimation') == '' ? 0 : $object_it->get('Estimation');
			     
			    echo $this->strategy->getDimensionText(round($value,1));
			    break;
			    
		    default:
		    	parent::drawCell( $object_it, $attr );
		}
	}

 	function getColumnWidth( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Progress':
 				return 60;
 			case 'Estimation':
 			    return 80;

 			case 'Workload':
 			case 'DeliveryDate':
 			case 'StartDate':
			case 'FinishDate':
		    case 'Stage':
 				return '7%';

 			default:
 				switch ( $attr )
 				{
 					case 'Importance':
 						return 90;
 					default:
 						return parent::getColumnWidth( $attr );
 				}
 		}
 	}

	function getRenderParms()
	{
		$parms = parent::getRenderParms();
		
		$this->buildRelatedDataCache();
		
		$this->visible_columns = array (
				'Type' => $this->getColumnVisibility( 'Type' )
		);
				
		return $parms; 
	}

 	protected function buildRelatedDataCache()
 	{
 		$this->request_non_terminal_states = getFactory()->getObject('Request')->getNonTerminalStates();
 		$this->trace_attributes = $this->getObject()->getAttributesByGroup('trace');
 	}
} 