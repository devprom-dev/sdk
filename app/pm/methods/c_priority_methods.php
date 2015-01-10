<?php
 
class ChangePriorityWebMethod extends WebMethod
{
     var $priority_it;
     
     function __construct( $priority_it = null )
     {
         $this->priority_it = $priority_it;
         
         parent::__construct();
     }
     
 	function execute_request() 
 	{
 		global $_REQUEST;

 		$this->execute($_REQUEST);
 	}
 	
 	function drawMethod( $object_it, $attribute ) 
 	{
 		echo '<div id="methodBody'.$object_it->getId().'" style="width:100%;" title="'.translate('Приоритет').'">';
 		    $this->drawBody( $object_it, $attribute );
 		echo '</div>';
 	}
 	
 	function execute ($parms) 
 	{
 		global $model_factory;
 		
 		$class = $model_factory->getObject($parms['class']);
 		$object_it = $class->getExact($parms['object']);
 		
 		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) return;
 		
		$value = $object_it->get($parms['attribute']);
		
		$this->priority_it = $object_it->object->getAttributeObject($parms['attribute'])->getAll();
		
		$this->priority_it->moveToId( $value );
		
		$current_pos = $this->priority_it->getPos();
		
		if ( $current_pos < $this->priority_it->count() && $parms['operation'] == 'dwn' )
		{
			$current_pos++;
		}

		if ( $current_pos > 0 && $parms['operation'] == 'up' )
		{
			$current_pos--;
		}
		
		$this->priority_it->moveToPos( $current_pos );
		
		$class->modify_parms($object_it->getId(),
			array($parms['attribute'] => $this->priority_it->getId() ), true);
 	}
 	
 	function getRedirectUrl()
 	{
 	    return "donothing";
 	}
 	
 	function drawBody( $object_it, $attribute ) 
 	{
 	    $this->object_it = $object_it;
 	    
		$this->priority_it->moveToId( $object_it->get($attribute) );

		$current_pos = $this->priority_it->getPos();
 		
		echo '<div class="no-carryng">';
		
		$parms['class'] = $object_it->object->getClassName();
		$parms['attribute'] = $attribute;
		$parms['object'] = $object_it->getId();
		$parms['operation'] = 'up';
		
		if ( $current_pos > 0 )
		{
			echo '<a href="'.$this->getJSCall($parms).'" tabindex="-1"><i class="icon-arrow-up"></i></a>';
		}
		
		echo '&nbsp;'.$this->priority_it->getDisplayName().'&nbsp;';

		$parms['operation'] = 'dwn';
		
		if ( $current_pos < $this->priority_it->count() - 1 )
		{
			echo '<a href="'.$this->getJSCall($parms).'" tabindex="-1"><i class="icon-arrow-down"></i></a>';
		}

		echo '</div>';
 	}
}
