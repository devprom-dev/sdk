<?php

class FieldFormButtons extends Field
{
	private $form = '';
	
	function __construct( $form )
	{
		$this->form = $form;
		parent::__construct();
	}
	
    function render( $view )
    {
        $this->form->drawButtonsOriginal();
        
        echo '<span class="clearfix"></span>';
        echo '<hr/>';
    }
}