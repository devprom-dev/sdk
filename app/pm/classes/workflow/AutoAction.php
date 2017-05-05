<?php
include 'AutoActionRegistry.php';
include 'AutoActionIterator.php';

class AutoAction extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_AutoAction', new AutoActionRegistry($this));
 		$this->setSortDefault(
 				array (
 						new SortOrderedClause()
 				)
 		);
 		$this->setAttributeVisible('OrderNum', false);
 		$this->setAttributeVisible('Conditions', false);
        $this->setAttributeType('EventType', 'REF_AutoActionEventId');
 	}
 	
 	function getSubjectClassName()
 	{
 		return '';
 	}
 	
 	function getActionAttributes()
 	{
        $attributes = array();
 		$subject = getFactory()->getObject($this->getSubjectClassName());
 		foreach( $subject->getAttributes() as $attribute => $data ) {
 			if ( $subject->getAttributeOrigin($attribute) != ORIGIN_CUSTOM ) continue;
 			$attributes[] = $attribute;
 		}
 		return $attributes;
 	}
 	
 	function getConditionAttributes()
 	{
 		return array_merge(
 				$this->getActionAttributes(),
 				array (
 						'Caption'
 				)
 		);
 	}
 	
 	function createIterator() 
 	{
 		return new AutoActionIterator( $this );
 	}
 	
 	function add_parms( $parms )
 	{
 		$parms['ClassName'] = strtolower($this->getSubjectClassName());

        if ( $parms['ReferenceName'] == '' ) {
            $this->serializeActions($parms);
            $this->serializeConditions($parms);
            $parms['ReferenceName'] = strtolower(uniqid(get_class($this)));
        }

 		$result = parent::add_parms( $parms );
 		
 		if ( $result > 0 && in_array($parms['EventType'], array(AutoActionEventRegistry::CreateAndModify, AutoActionEventRegistry::CreateOnly)) )
 		{
	 		getFactory()->getObject('StateAction')->add_parms(
	 				array (
	 					'State' => WorkflowScheme::Instance()->getStateIt(getFactory()->getObject($this->getSubjectClassName()))->getId(),
	 					'ReferenceName' => $parms['ReferenceName']  
	 				)
	 		);
 		}
 		
 		return $result;
 	}

 	function modify_parms( $id, $parms )
 	{
 		$this->serializeActions($parms);
 		$this->serializeConditions($parms);
 		
 		return parent::modify_parms( $id, $parms );
 	}
 	
 	function delete( $id, $record_version = ''  )
 	{
 		$action_it = getFactory()->getObject('StateAction')->getRegistry()->Query(
 				array ( 
 					new StateActionReferencePredicate($this->getExact($id)->get('ReferenceName')),
 					new FilterBaseVpdPredicate()
 				)
 			);
 		while( !$action_it->end() )
 		{
 			$action_it->object->delete($action_it->getId());
 			$action_it->moveNext();
 		}
 		
 		return parent::delete($id);
 	}

 	protected function serializeActions( &$parms )
 	{
 		$data = array();
 		foreach( $this->getActionAttributes() as $attribute )
 		{
 		    if ( $attribute == 'State' ) {
                $data[$attribute] = getFactory()->getObject('pm_State')
                    ->getExact($parms[$attribute])->get('ReferenceName');
            }
            else {
                $data[$attribute] = $parms[$attribute];
            }
 			unset($parms[$attribute]);
 		}
 		$parms['Actions'] = JsonWrapper::encode($data);
 	}
 	
 	protected function serializeConditions( &$parms )
 	{
 		$items = array();
 		for( $i = 0; $i < 3; $i++ )
 		{
 			if ( $parms['Operator'.$i] == '' || $parms['Condition'.$i] == '' ) continue; 
 			$items[] = array (
 					'Condition' => $parms['Condition'.$i],
 					'Operator' => $parms['Operator'.$i],
 					'Value' => IteratorBase::wintoutf8($parms['Value'.$i])
 			);
 		}
 		$conditions = array (
 				'mode' => $parms['ConditionsMode'],
 				'items' => $items 
 		);
 		$parms['Conditions'] = JsonWrapper::encode($conditions);
 	}
}
