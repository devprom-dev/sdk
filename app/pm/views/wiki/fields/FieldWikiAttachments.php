<?php
include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';

class FieldWikiAttachments extends FieldAttachments
{
 	function getAttachmentIt()
    {
        if ( $this->getObjectIt()->getId() > 0 ) {
            return getFactory()->getObject('WikiPageFile')->getRegistry()->Query(
                array(
                    new FilterVpdPredicate(),
                    new FilterAttributePredicate('WikiPage', $this->getObjectIt()->getId())
                )
            );
        }
        else {
            return getFactory()->getObject('WikiPageFile')->getEmptyIterator();
        }
    }
}