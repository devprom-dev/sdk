<?php

class FieldRestCache extends Field
{
	private $url = '';
	
	function __construct( $url )
	{
		$this->url = $url;
		parent::__construct();
	}
	
    function render( $view )
    {
        echo '<a action="reset-cache" class="btn btn-danger pull-left" href="'.$this->url.'">'.text(3006).'</a>';
    }
}