<?php

class FieldWidgetUrl extends Field
{
	private $object_it = null;

	function __construct( $object_it )
	{
		parent::__construct();
		$this->object_it = $object_it;
	}

	function render( $view ) {
		echo '<div class="input-block-level well well-text">';
			echo '<img class="widget-list" src="/images/table.png">';
			echo '<a href="'.$this->object_it->get('Url').'">'.$this->object_it->getDisplayName().'</a>';
		echo '</div>';
	}
}