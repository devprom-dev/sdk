<?php

class FieldText extends Field
{
 	var $rows = 2;
 	var $class_name;
	private $wrap = true;

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

	function setWrap( $flag = true ) {
		$this->wrap = $flag;
	}

 	function readOnly()
 	{
 	    return !$this->getEditMode() || parent::readOnly();
 	}
 	
 	function draw( $view = null )
	{
		if ( $this->readOnly() )
		{
		    echo '<span class="input-block-level well well-text" style="width:100%;height:auto;word-break: break-all;">';
		        echo $this->getText();
		    echo '</span>';
        	echo '<input type="hidden" name="'.$this->getName().'" value="'.htmlentities($this->getValue()).'">';
		}
		else
		{
			
			if ( $this->rows == 1 )
			{
				echo '<input id="'.$this->getId().'" name="'.$this->getName().'" class="input-block-level" type="text" tabindex="'.$this->getTabIndex().'" placeholder="'.htmlentities($this->getDefault()).'" value="'.htmlentities($this->getValue()).'" '.($this->getRequired() ? 'required' : '').' autocomplete="nope" autocorrect="off" autocapitalize="off" spellcheck="false">';
			}
			else
			{
			echo '<div class="for-textarea">';
			    ?>
		        <textarea class="<?php echo $this->getClassName() ?>" id="<? echo $this->getId() ?>" name="<? echo $this->getName(); ?>" rows="<?php echo $this->getRows(); ?>" tabindex="<? echo $this->getTabIndex() ?>" <?=($this->getRequired() ? 'required' : '')?> <?=($this->wrap ? : 'wrap="off"')?> ><? echo $this->getValue(); ?></textarea>
				<?
				echo '</div>';
			}
			
		}
	}
}