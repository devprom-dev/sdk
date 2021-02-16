<?php
include "CommentsFormMinimal.php";
include "CommentsTable.php";
include "CommentsPageSettingBuilder.php";

class CommentsPage extends PMPage
{
    function __construct()
    {
        getSession()->addBuilder(new CommentsPageSettingBuilder());
        parent::__construct();
    }

    function getObject() {
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

 	function getTable() {
        return new CommentsTable($this->getObject());
    }

    function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array(
                'context_template' => ''
            )
        );
    }
}
