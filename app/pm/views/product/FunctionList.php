<?php

if ( !class_exists('IssuesProgressFrame', false) )
{
    include SERVER_ROOT_PATH.'pm/views/c_request_frame.php';
}

class FunctionList extends PMPageList
{
 	var $request_agg_it, $strategy;
 	
 	private $request_non_terminal_states = array();
 	
 	function __construct( $object )
 	{
 	    parent::__construct( $object );
 	    
 	    $this->buildData();
 	}
 	
 	function buildData()
 	{
 		$this->request_non_terminal_states = getFactory()->getObject('Request')->getNonTerminalStates();
 	}
 	
	function retrieve()
	{
		global $model_factory, $project_it;

		parent::retrieve();
		
		$it = $this->getIteratorRef();

		// cache requests per feature
		$filters = $this->getFilterValues();
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->addFilter( new RequestStagePredicate($filters['stage']) );

		$request->addFilter( new StatePredicate('notresolved') );
		
		$this->strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		
		$this->request_agg_it = $request->getRequestsAggByFunction();
		
		$this->request_agg_it->buildPositionHash( array('Function') );
	}

	function getSorts()
	{
		$sorts = parent::getSorts();
		
		$values = $this->getFilterValues();
		
		if ( $values['view'] == 'chart' )
		{
			array_push( $sorts, new SortFeatureStartClause() );
		}
		else
		{
			array_push( $sorts, new SortAttributeClause('Caption') );
		}

		return $sorts;
	}

	function setupColumns()
	{
		$this->object->addAttribute('UncompletedIssues', '', text(1342), false, false, '', 40);
		
		parent::setupColumns();
		
		$values = $this->getFilterValues();
		
		$this->object->setAttributeVisible( 'UncompletedIssues', $values['state'] != 'closed' );
		
		$this->object->setAttributeVisible( 'Request', $values['state'] == 'closed' );
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
		
		if( $attr == 'Description' ) 
		{
			drawMore($object_it, 'Description', 12);
		}
		elseif( $attr == 'Progress' ) 
		{
    		$filters = $this->getFilterValues();
    		
    		$request = $model_factory->getObject('pm_ChangeRequest');
    		
    		$request->addFilter( new RequestStagePredicate($filters['stage']) );
    		
    		$request->addFilter( new FilterAttributePredicate('Function', $object_it->getId()) );
		    
			echo '<div style="padding:2px 8px 0 0;">';
				$frame = new IssuesProgressFrame( $request->getAll()->getProgress(), false );

				$frame->draw();
			echo '</div>';
		}
		elseif( $attr == 'UncompletedIssues' ) 
		{
			$this->request_agg_it->setStop( 'Function', $object_it->getId() );

			$session = getSession();
			
			$values = $this->getFilterValues();
			
			$report_it = $model_factory->getObject('PMReport')->getExact('allissues');
			
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
		}
		elseif( $attr == 'Workload' )
		{
		    $value = $object_it->get('Workload') == '' ? 0 : $object_it->get('Workload');
		     
		    echo round($value, 1).' '.translate('дн.');
		    
		    if ( $value > 0 )
		    {
		        echo ' ('.$this->strategy->getDimensionText(round($object_it->get('EstimationLeft'),1)).')';
		    }
		}
		elseif( $attr == 'Estimation' )
		{
		    $value = $object_it->get('Estimation') == '' ? 0 : $object_it->get('Estimation');
		     
		    echo $this->strategy->getDimensionText(round($value,1));
		}
		else 
		{
			parent::drawCell( $object_it, $attr );
		}
	}

	function getItemActions( $column_name, $object_it ) 
	{
		global $model_factory;
		
		$actions = parent::getItemActions( $column_name, $object_it );
		
		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_ChangeRequest'));

		$method->setRedirectUrl('donothing');
		
		$create_actions = array();
		
		if ( $method->hasAccess() )
		{
		    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();

		    $parms = array (
		    		'Function' => $object_it->getId(),
		    		'area' => $this->getTable()->getPage()->getArea()
		    );
		    
			$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
			    		array (
			    				new FilterBaseVpdPredicate()
			    		)
		    	);
			
			while ( !$type_it->end() )
			{
				$parms['Type'] = $type_it->getId();
				
				$create_actions[] = array ( 
			        'name' => translate($type_it->getDisplayName()),
					'url' => $method->getJSCall($parms)
				);
				
				$type_it->moveNext();
			}

			unset($parms['Type']);
			
			$create_actions[] = array ( 
		        'name' => $method->getObject()->getDisplayName(),
				'url' => $method->getJSCall($parms)
			);
		}

		if ( count($create_actions) > 0 )
		{
			$actions[] = array ( 
				'name' => translate('Создать'),
	            'items' => $create_actions
			);
		}
		
		$report_it = getFactory()->getObject('PMReport')->getExact('allissues');

		if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		
		$actions[] = array(
		    'url' => $report_it->getUrl().'&state=all&function='.$object_it->getId(), 
		    'name' => translate('Перейти к пожеланиям')
		);

		return $actions;
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
		    case 'Request':
 			    return '10%';
 			    
 			case 'DeliveryDate':
 			case 'StartDate':
		    case 'UncompletedIssues':
		    case 'Stage':
 				return '8%';

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
} 