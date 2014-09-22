<?php

class SpentTimeForm extends PMPageForm
{
	private $anchor_it;
	
	public function setAnchorIt( $anchor_it )
	{
		$this->anchor_it = $anchor_it;
	}
	
	public function getLeftFieldName()
	{
		return is_a($this->anchor_it->object, 'Request') ? 'EstimationLeft' : 'LeftWork';
	}
	
	function IsAttributeVisible( $attr_name )
	{
		switch ( $attr_name ) 
		{
		    case 'ReportDate':
		    case 'Description':
		    case 'Capacity':
		    	return true;
		    	
		    case 'LeftWork':
		    	return $this->anchor_it->object->getAttributeType($this->getLeftFieldName()) != ''
		    		&& getSession()->getProjectIt()->getMethodologyIt()->TaskEstimationUsed();
		    	
		    default:
		    	return false;
		}
	}
	
	function IsAttributeRequired( $attr_name )
	{
		switch ( $attr_name ) 
		{
		    case 'ChangeRequest':
		    	return true;

		    default:
		    	return parent::IsAttributeRequired( $attr_name );
		}
	}
	
	function getFieldValue( $attr )
	{
	    switch ( $attr )
	    {
	        case 'ChangeRequest':
	        case 'Task':

	        	return $this->anchor_it->getId();
	            
			case 'ReportDate':
				
				return getSession()->getLanguage()->getDateFormatted( date('Y-m-d') );

			case 'LeftWork':
			    
				return $this->anchor_it->get($this->getLeftFieldName());
	        	
	        default:
	            
	        	return parent::getFieldValue( $attr );
	    }
	}
	
	function createField( $attr )
	{
		$field = parent::createField( $attr );
		
		if ( $attr == 'LeftWork' )
		{
			$field->setDefault( $this->anchor_it->get($this->getLeftFieldName()) );
		}
		
		return $field;
	}
	
	function drawScripts()
	{
		?>
		<script type="text/javascript">
			$(document).ready( function() {
				$('#<?=$this->getId()?> #pm_ActivityCapacity').on('keydown', function() {
					updateLeftWork($(this), $('#<?=$this->getId()?> #pm_ActivityLeftWork'));
				});	
			});
		</script>
		<?php
	}
}