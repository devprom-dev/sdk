<?php

include_once 'CommentList.php';

class PageSectionComments extends InfoSection
{
 	var $object_it;
 	
 	private $baseline = '';
 	
 	function PageSectionComments( $object_it, $baseline = '' )
 	{
 		$this->object_it = $object_it;
 		
 		$this->baseline = $baseline;
 		
 		parent::InfoSection();
 	}
 	
 	function getCaption()
 	{
 		return translate('Комментарии');
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}

 	function getRenderParms()
	{
		global $model_factory;
		
		return array_merge( parent::getRenderParms(), array (
			'section' => $this
		));
	}
 	
 	function getTemplate()
	{
		return 'pm/PageSectionComments.php';
	}
 	
 	function render( &$view, $parms = array() )
 	{
		$comment_list = new CommentList( $this->object_it, $this->baseline );

		$comment_list->render( $view, $parms );
	}
}  
