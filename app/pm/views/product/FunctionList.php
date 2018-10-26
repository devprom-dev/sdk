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
		$request->addFilter( new StatePredicate('notresolved') );
		
		$this->strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		$this->request_agg_it = $request->getRequestsAggByFunction();
		$this->request_agg_it->buildPositionHash( array('Function') );
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
		parent::setupColumns();
		
		$values = $this->getFilterValues();
		
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
                parent::drawCell($object_it, 'Caption');
                parent::drawCell($object_it, 'DescriptionWithInCaption');
				break;

		    case 'Caption':
                echo $object_it->get($this->visible_columns['Type'] ? 'Caption' : 'CaptionAndType');
                if ( $this->checkColumnHidden('Tags') && $object_it->get('Tags') != '' ) {
                    echo ' ';
                }
                parent::drawCell($object_it, 'DescriptionWithInCaption');
    		    break;
		    	
		    case 'Progress':
                $frame = new IssuesProgressFrame(
                    getFactory()->getObject('Request')->getRegistry()->Query(
                            array (
                                new RequestFeatureFilter($object_it->getId())
                            )
                        )->getProgress(), false
                );
                $frame->draw();
				break;

            case 'ProgressReq':
                $frame = new IssuesProgressFrame(
                    array(
                        'R' => array(
                            $object_it->get('TotalRequirements'),
                            $object_it->get('CompletedRequirements')
                        )
                    ), false
                );
                $frame->draw();
                break;

		    case 'Workload':
			    $value = $object_it->get('Workload') == '' ? 0 : $object_it->get('Workload');
			    echo round($value, 1).' '.translate('дн.');
			    if ( $value > 0 ) {
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

    function getColumnFields()
    {
        return array_merge(
            parent::getColumnFields(),
            array(
                'OrderNum'
            )
        );
    }
}