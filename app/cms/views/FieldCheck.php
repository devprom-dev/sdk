<?php

class FieldCheck extends Field
{
 	var $checkName;
	
	function FieldCheck( $checkText )
	{
		$this->checkName = $checkText;
		
		parent::Field();
	}

	function readOnly()
	{
	    return !$this->getEditMode() || parent::readOnly();
	}
	
 	function draw()
	{
	?>
		<label class="checkbox">
			<input type="hidden" name="<? echo $this->getName().'OnForm'; ?>" value="Y"> 
			<input id="<? echo $this->getId() ?>" tabindex="<? echo $this->getTabIndex() ?>" class=checkbox name="<? echo $this->getName(); ?>" type="checkbox" <? if($this->getValue() == 'Y' || $this->getValue() == 'on') echo 'checked'; ?>
			   <? echo ($this->readOnly() ? 'class="readonly" disabled' : '') ?>><? echo_lang($this->checkName); ?>
		</label>
	<?
	}
	
	function getCheckName()
	{
		return $this->checkName;
	}
}
