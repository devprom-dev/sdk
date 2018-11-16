<?php

class CommentAuthorPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			"IFNULL((SELECT u.Caption FROM cms_User u WHERE u.cms_UserId = t.AuthorId), CONCAT_WS('', t.ExternalAuthor, IF(t.ExternalEmail IS NOT NULL,CONCAT(' &lt;', t.ExternalEmail, '&gt;'),''))) AuthorName ";

 		$columns[] =  
 			"IFNULL((SELECT u.Email FROM cms_User u WHERE u.cms_UserId = t.AuthorId), t.ExternalEmail) AuthorEmail ";

		$columns[] =
			" ( SELECT u.cms_UserId FROM cms_User u WHERE u.cms_UserId = t.AuthorId AND u.PhotoPath IS NOT NULL) AuthorPhotoId ";

 		return $columns;
 	}

 	function afterDelete($object_it)
    {
        // remove comment tags from wysiwyg fields;
        $this->commentId = $object_it->getId();
        $anchorIt = $object_it->getAnchorIt();

        if ( $anchorIt->getId() != '' ) {
            $data = $anchorIt->getData();
            $parms = array();
            foreach( $anchorIt->object->getAttributesByType('wysiwyg') as $attribute ) {
                $parms[$attribute] = preg_replace_callback(
                    REGEX_COMMENTS, array($this, 'removeComments'), html_entity_decode($data[$attribute])
                );
                if ( $parms[$attribute] == '' ) unset($parms[$attribute]);
            }
            if ( count($parms) > 0 ) {
                $anchorIt->object->setNotificationEnabled(false);
                $anchorIt->object->getRegistry()->Store($anchorIt, $parms);
            }
        }

        parent::afterDelete($object_it);
    }

    function removeComments( $match )
    {
        if ( $match[1] == $this->commentId ) {
            return $match[2];
        }
        else {
            return $match[0];
        }
    }
}
