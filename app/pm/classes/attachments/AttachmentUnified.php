<?php
include "AttachmentUnifiedIterator.php";
include "AttachmentUnifiedRegistry.php";
include "predicates/AttachmentClassPredicate.php";

class AttachmentUnified extends Metaobject
{
 	function __construct()
	{
 		parent::Metaobject('pm_Attachment', new AttachmentUnifiedRegistry($this));
		$this->setSortDefault(
				array (
					new SortRecentClause()
				)
		);
		$this->setAttributeCaption('ObjectId', translate('Артефакт'));
        $this->setAttributeType('ObjectId', 'VARCHAR');
		$this->setAttributeVisible('ObjectId', true);
		$this->setAttributeVisible('Description', false);

        $this->setAttributeType('ObjectClass', 'REF_AttachmentEntityId');
        $this->setAttributeCaption('ObjectClass', text(2098));

        foreach( array('FilePath','FileExt') as $attribute ) {
            $this->addAttributeGroup($attribute,'system');
        }

		foreach( array('ObjectClass','ObjectId','FileSize') as $attribute ) {
			$this->addAttributeGroup($attribute,'nonbulk');
		}
 	}
 	
 	function createIterator() {
 		return new AttachmentUnifiedIterator( $this );
 	}

    function getVpds() {
        return array();
    }

    function delete($object_id, $record_version = '')
    {
        $objectIt = $this->getExact($object_id);
        if ( $objectIt->get('AttachmentClassName') != $this->getEntityRefName() ) {
            return getFactory()->getObject('WikiPageFile')->delete($objectIt->get('SelfId'));
        }
        else {
            return getFactory()->getObject('Attachment')->delete($objectIt->get('SelfId'));
        }
    }
}