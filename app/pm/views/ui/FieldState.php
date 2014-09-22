<?php

class FieldState extends Field
{
 	function draw()
	{
		echo '<span class="label label-warning" id="'.$this->getId().'">'.$this->getText().'</span>';
	}
}
