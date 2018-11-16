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
 	}

 	function getAttributeObject($attribute)
    {
        $object = parent::getAttributeObject($attribute);
        $object->addFilter( new FilterBaseVpdPredicate() );
        return $object;
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
            $parms['ReferenceName'] = strtolower(uniqid(get_class($this)));
        }
        if ( $parms['Actions'] == '' ) {
            $this->serializeActions($parms);
        }
        if ( $parms['Conditions'] == '' ) {
            $this->serializeConditions($parms);
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

 		$result = parent::modify_parms( $id, $parms );

 		if ( $parms['OrderNum'] != '' ) {
            DAL::Instance()->Query("SET @r=0");
            DAL::Instance()->Query("UPDATE ".$this->getEntityRefName()." t SET t.OrderNum = @r:= (@r+1) WHERE t.VPD = '".$this->getVpdValue()."' ORDER BY t.OrderNum ASC");

            $sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
                " SELECT NOW(), NOW(), t.VPD, t.".$this->getIdAttribute().", '".get_class($this)."' ".
                "     FROM ".$this->getEntityRefName()." t WHERE t.VPD = '".$this->getVpdValue()."' ";
            DAL::Instance()->Query( $sql );
        }

        return $result;
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
 		foreach( $this->getActionAttributes() as $attribute ) {
 		    if ( !array_key_exists($attribute, $parms) ) continue;
 		    if ( $attribute == 'State' ) {
                $data[$attribute] = getFactory()->getObject('pm_State')
                    ->getExact($parms[$attribute])->get('ReferenceName');
            }
            else {
                $data[$attribute] = $parms[$attribute];
            }
 			unset($parms[$attribute]);
 		}
 		if ( count($data) > 0 ) {
            $parms['Actions'] = JsonWrapper::encode($data);
        }
 	}
 	
 	protected function serializeConditions( &$parms )
 	{
 		$items = array();
 		for( $i = 0; $i < 30; $i++ )
 		{
 			if ( $parms['Operator'.$i] == '' || $parms['Condition'.$i] == '' ) continue; 
 			$items[] = array (
 					'Condition' => $parms['Condition'.$i],
 					'Operator' => $parms['Operator'.$i],
 					'Value' => IteratorBase::wintoutf8($parms['Value'.$i])
 			);
 		}
 		if ( count($items) < 1 ) return;

 		$parms['Conditions'] = JsonWrapper::encode(
            array (
                'mode' => $parms['ConditionsMode'],
                'items' => $items
            )
        );
 	}

    function getOrderStep() {
        return 1;
    }
}
