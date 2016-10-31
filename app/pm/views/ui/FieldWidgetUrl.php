<?php

class FieldWidgetUrl extends Field
{
	private $object_it = null;
    private $description = '';

	function __construct( $object_it, $description = '' )
	{
		parent::__construct();
		$this->object_it = $object_it;
        $this->description = $description;
	}

	function render( $view ) {
		echo '<span class="" style="margin-left:0;">';
			echo '<ul class="nav nav-pills nav-stacked" style="margin-bottom:0px;">';
		    	echo '<li><a href="'.$this->object_it->get('Url').'" style="margin-left:-12px;"><strong>'.$this->object_it->getDisplayName().'</strong></a><p>'.$this->description.'</p></li>';
            echo '</ul>';
		echo '</span>';
	}
}