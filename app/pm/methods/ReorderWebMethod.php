<?php

class ReorderWebMethod extends AutoSaveFieldWebMethod
{
	private $modify_method = null;
	
	function __construct( $object_it = null )
	{
		parent::__construct($object_it, 'OrderNum');
		
		if ( is_object($object_it) )
		{
			$this->modify_method = new ModifyAttributeWebMethod($object_it, 'OrderNum');
		}
	}
	
	function draw()
	{
		$this->modify_method->setObjectIt($this->getObjectIt());
		$this->modify_method->setCallback( "donothing" );
		
		echo '<div class="reorder-control" style="width:90px;">';
			$this->modify_method->setValue(1);
			echo '<div><a href="'.$this->modify_method->getJSCall(array('type'=>'dec')).'" tabindex="-1"><i class="icon-arrow-up"></i></a></div>';
			
			echo '<div style="width:50px;">';
				parent::draw();
			echo '</div>';
			
			$this->modify_method->setValue(1);
			echo '<div><a class="pull-right" href="'.$this->modify_method->getJSCall(array('type'=>'inc')).'" tabindex="-1"><i class="icon-arrow-down"></i></a></div>';
		echo '</div>';
	}
	
}