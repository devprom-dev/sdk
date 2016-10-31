<?php

class FieldUID extends FieldShortText
{
	private $object_it = null;

	function __construct( $object_it )
	{
		parent::__construct();
		$this->object_it = $object_it;
	}

	function render( $view )
	{
		$uid = new ObjectUID();
		$info = $uid->getUIDInfo($this->object_it);
		echo '<div class="input-block-level well well-text">';
			echo $view->render('core/Clipboard.php', array ('url' => $info['url'], 'uid' => $info['uid']));
		echo '</div>';
	}
}