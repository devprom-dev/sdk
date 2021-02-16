<?php
include_once SERVER_ROOT_PATH.'pm/views/issues/IssuesProgressFrame.php';

class FunctionList extends PMPageList
{
 	private $strategy;
 	private $visible_columns = array();

 	function __construct( $object )
 	{
 	    parent::__construct( $object );
        $this->strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
 	}

 	function extendModel()
    {
        $this->getObject()->addAttribute('FeatureLevel', 'REF_FeatureTypeId', translate('Уровень'), false);
        $this->getObject()->addAttributeGroup('Type', 'system');

        parent::extendModel();
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

    function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
		    case 'Caption':
                echo $object_it->get($this->visible_columns['Type'] ? 'Caption' : 'CaptionAndType');
                if ( $this->checkColumnHidden('Tags') && $object_it->get('Tags') != '' ) {
                    echo ' ';
                }
                parent::drawCell($object_it, 'DescriptionWithInCaption');
    		    break;
		    	
            case 'Progress':
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
		
		$this->visible_columns = array (
            'Type' => $this->getColumnVisibility( 'Type' )
		);
				
		return $parms; 
	}
}