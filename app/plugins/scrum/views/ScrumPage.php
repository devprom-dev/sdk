<?php
include "ScrumForm.php";
include "ScrumTable.php";

class ScrumPage extends PMPage
{
    function __construct()
    {
        parent::__construct();

        if ( $this->needDisplayForm() )	{
            $object_it = $this->getObjectIt();
            if ( is_object($object_it) && $object_it->count() > 0 ) {
                $this->addInfoSection( new PageSectionComments($object_it, $this->getCommentObject()) );
            }
        }
    }

    function getObject() {
 		return getFactory()->getObject('pm_Scrum');
	}
	
 	function getTable() {
 		return new ScrumTable( new Scrum(new ScrumGrouppedRegistry()) );
 	}
 	
 	function getEntityForm() {
 		return new ScrumForm( $this->getObject() );
 	}
}