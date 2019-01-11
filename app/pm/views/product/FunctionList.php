<?php

if ( !class_exists('IssuesProgressFrame', false) )
{
    include SERVER_ROOT_PATH.'pm/views/c_request_frame.php';
}

class FunctionList extends PMPageList
{
 	private $strategy;
 	private $request_non_terminal_states = array();
 	private $visible_columns = array();
 	private $trace_attributes = array();

 	function __construct( $object )
 	{
 	    parent::__construct( $object );
        $this->strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
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