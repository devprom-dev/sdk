<?php

class FieldTextStatic extends Field
{
 	function draw()
	{
		$value = $this->getText();

		echo '<div class="readonly">';
			echo ($value == '' ? ' &nbsp; ' : str_replace(chr(10), '<br/>', $value));
		echo '</div>';
		
		echo '<input id="'.$this->getId().'" name="'.$this->getName().'" type="hidden" value="'.$this->getEncodedValue().'">';
	}
}
