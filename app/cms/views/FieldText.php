<?php

class FieldText extends Field
{
 	var $rows = 2;
 	var $class_name;

 	function setRows( $rows )
 	{
 		$this->rows = $rows;
 	}
 	
 	function getRows()
 	{
 		return $this->rows;
 	}
 	
 	function setClassName( $class )
 	{
 		$this->class_name = $class;
 	}
 	
 	function getClassName()
 	{
 		return $this->class_name;
 	}
 	
 	function readOnly()
 	{
 	    return !$this->getEditMode() || parent::readOnly();
 	}
 	
 	function draw()
	{
		if ( $this->readOnly() )
		{
		    echo '<span id="'.$this->getId().'" class="input-block-level well well-text" style="width:100%;height:auto;word-break: break-all;">';
		        echo $this->getText();
		    echo '</span>';

        	echo '<input type="hidden" name="'.$this->getName().'" value="'.$this->getValue().'">';
		}
		else
		{
			
			if ( $this->rows == 1 )
			{
				echo '<input id="'.$this->getId().'" name="'.$this->getName().'" class="input-block-level" type="text" tabindex="'.$this->getTabIndex().'" placeholder="'.$this->getDefault().'" value="'.$this->getValue().'" '.($this->getRequired() ? 'required' : '').' >';
			}
			else
			{
			echo '<div class="for-textarea">';
			    ?>
		        <textarea class="<?php echo $this->getClassName() ?>" id="<? echo $this->getId() ?>" name="<? echo $this->getName(); ?>" rows="<?php echo $this->getRows(); ?>" tabindex="<? echo $this->getTabIndex() ?>" placeholder="<?php echo $this->getDefault() ?>" <?=($this->getRequired() ? 'required' : '')?> ><? echo $this->getValue(); ?></textarea>
				<?
				echo '</div>';
			}
			
		}
	}
}