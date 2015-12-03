<?php

include_once 'CommentList.php';

class PageSectionComments extends InfoSection
{
 	var $object_it;
 	
 	private $baseline = '';
	private $title = '';
	private $id = '';
 	
 	function PageSectionComments( $object_it, $baseline = '' )
 	{
 		$this->object_it = $object_it;
 		$this->baseline = $baseline;
		$this->setCaption(translate('Комментарии'));
		$this->setId( parent::getId() );

 		parent::__construct();
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
