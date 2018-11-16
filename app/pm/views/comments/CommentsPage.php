<?php
include_once "CommentsFormMinimal.php";

class CommentsPage extends PMPage
{
	function getObject() 
	{
		return getFactory()->getObject('Comment');
	}

 	function getForm() 
 	{
 		$form = new CommentsFormMinimal( $this->getObject() );

        $class_name = getFactory()->getClass($_REQUEST['class']);
        if ( class_exists($class_name, false) ) {
            $target = getFactory()->getObject($class_name);
            $form->setAnchorIt($target->getExact($_REQUEST['object']));
        }

 		return $form;
 	}
}
