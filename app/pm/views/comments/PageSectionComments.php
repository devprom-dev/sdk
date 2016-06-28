<?php

include_once 'CommentList.php';

class PageSectionComments extends InfoSection
{
 	var $object_it;
 	
 	private $baseline = '';
	private $title = '';
	private $id = '';
 	
 	function __construct( $object_it, $baseline = '' )
 	{
		parent::__construct();

 		$this->object_it = $object_it;
 		$this->baseline = $baseline;
		$this->setCaption(translate('Комментарии'));
		$this->setId( parent::getId() );
		$this->setPlacement('bottom');
 	}

	function setCaption( $title ) {
		$this->title = $title;
	}

 	function getCaption() {
 		return $this->title;
 	}

	function setId( $id ) {
		$this->id = $id;
	}

	function getId() {
		return $this->id;
	}

 	function getObjectIt()
 	{
 		return $this->object_it;
 	}

 	function getRenderParms()
	{
		return array_merge( parent::getRenderParms(), array (
			'section' => $this
		));
	}
 	
 	function getTemplate()
	{
		return 'pm/PageSectionComments.php';
	}
 	
 	function render( $view, $parms = array() )
 	{
		$comment_list = new CommentList( $this->object_it, $this->baseline );
		$comment_list->render( $view, $parms );
	}
}  
