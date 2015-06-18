<?php

include "MethodologyIterator.php";

class Methodology extends Metaobject
{
 	function __construct() 
 	{
		parent::Metaobject('pm_Methodology');
		
		$this->setAttributeDescription('IsBlogUsed', text(679));
		$this->setAttributeDescription('IsKnowledgeUsed', text(678));
	}
	
	function createIterator() 
	{
		return new MethodologyIterator( $this );
	}
	
 	function IsDeletedCascade( $object )
	{
		return false;
	}
	
	function getPage()
	{
	    $session = getSession();
	    
		return $session->getApplicationUrl().'project/methodology?';
	}
	
	function modify_parms( $object_id, $parms )
	{
		$result = parent::modify_parms( $object_id, $parms );
		
		if ( $result < 1 ) return $result;
		
		$methodology_it = $this->getExact($object_id);
		
		if ( $methodology_it->get('IsRequestOrderUsed') == 'Y' )
		{
			// fix unordered tasks and requests
			$object = new Metaobject('pm_ChangeRequest');
			
			$object->removeNotificator( 'ObjectFactoryNotificator' );
			$object->addFilter( new FilterAttributePredicate('OrderNum', '0') );
			
			$seq_it = $object->getAll();
			$next_seq = 1;
			
			while( !$seq_it->end() )
			{
				$object->modify_parms($seq_it->getId(), array('OrderNum' => $next_seq));

				$next_seq++;
				$seq_it->moveNext();
			}

			$object = new Metaobject('pm_Task');
			
			$object->removeNotificator( 'ObjectFactoryNotificator' );
			$object->addFilter( new FilterAttributePredicate('OrderNum', '0') );
			
			$seq_it = $object->getAll();
			$next_seq = 1;
			
			while( !$seq_it->end() )
			{
				$object->modify_parms($seq_it->getId(), array('OrderNum' => $next_seq));

				$next_seq++;
				$seq_it->moveNext();
			}
		}
		
		return $result;
	}
}