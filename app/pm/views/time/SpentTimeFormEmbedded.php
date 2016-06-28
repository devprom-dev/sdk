<?php

class SpentTimeFormEmbedded extends PMFormEmbedded
{
 	var $anchor_it;
 	
 	function setAnchorIt( $anchor_it )
 	{
 		$this->anchor_it = $anchor_it;
 	}
 	
	function getLeftWorkAttribute()
	{
	    return $this->left_work_attribute;
	}
	
	function setLeftWorkAttribute( $attribute )
	{
	    $this->left_work_attribute = $attribute;
	}
	
	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'LeftWork':
 			    return $this->anchor_it->object->getAttributeType($this->getLeftWorkAttribute()) != ''
 			    	&& getSession()->getProjectIt()->getMethodologyIt()->TaskEstimationUsed();
 			    
 			default:
 				return parent::IsAttributeVisible( $attribute );
 		}
 	}
 	
	function getAttributeType( $attr )
	{
 		switch ( $attr )
 		{
 			default:
 				return parent::getAttributeType( $attr );
 		}
	}
 	
	function getAttributeObject( $attr )
	{
 		switch ( $attr )
 		{
 			default:
 				return parent::getAttributeObject( $attr );
 		}
	}
	
	function getFieldValue( $attr )
	{
		switch ( $attr )
		{
			case 'ReportDate':
			    
				return getSession()->getLanguage()->getDateFormatted( date('Y-m-d') );

			case 'LeftWork':
			    
			    return $this->anchor_it->get($this->getLeftWorkAttribute());
			    
			default:
				
			    if ( $attr == $this->getAnchorField() )
				{
					return $this->anchor_it->getId();
				}
				
				return parent::getFieldValue( $attr );
		}
	}
 	
	function getSaveCallback()
	{
		return 'updateLeftWorkAttribute';
	}
	
 	function drawField( $attr, $type, $value, $tabindex )
 	{
 		$field_name = $this->getFieldName( $attr );
 		
 		switch ( $attr )
 		{
 		    case 'LeftWork':
 		        break;
 		        
 			case 'Capacity':
 				if ( $this->object->getAttributeType('LeftWork') == '' )
 				{
 					return parent::drawField( $attr, $type, $value, $tabindex );
 				}
 				
 				echo '<div class="row-fluid">';
    				echo '<div class="'.($this->IsAttributeVisible('LeftWork') ? 'span6' : '').'">';
    	 				echo '<div class="line">';
    						echo translate($this->object->getAttributeUserName('Capacity')).text(2191);
    					echo '</div>';
    					$script = "javascript: updateLeftWork($('#".$field_name."'), $('#".$this->getFieldName('LeftWork')."'));";
    
    					echo '<input type="text" class="spent-time input-block-level" id="'.$field_name.'" name="'.$field_name.'" default="'.$value.'" tabindex="'.$tabindex.'" onkeydown="'.$script.'" title="'.htmlentities($this->object->getAttributeDescription('Capacity')).'">';
    				echo '</div>';
				
    				if ( $this->IsAttributeVisible('LeftWork') )
    				{
    					echo '<div class="span6">';
    						echo '<div class="line">';
    							echo translate($this->object->getAttributeUserName('LeftWork'));
    						echo '</div>';
    
    						echo '<input type="text" class="spent-time input-block-level" id="'.$this->getFieldName('LeftWork').'" name="'.$this->getFieldName('LeftWork').'" default="'.$this->getFieldValue('LeftWork').'" tabindex="'.($tabindex+1).'">';
    					echo '</div>';
    				}
    			echo '</div>';
				
				echo '<div style="clear:both;"></div>';
				
				break;				
 				
			default:
 				return parent::drawField( $attr, $type, $value, $tabindex );
 		}
 	}
}
