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
        $this->addAttribute('Size', 'VARCHAR', translate('Размер'), true);
		$this->setAttributeCaption('ObjectId', translate('Артефакт'));
		$this->setAttributeVisible('ObjectId', true);
		$this->setAttributeVisible('Description', false);

        $this->setAttributeType('ObjectClass', 'REF_AttachmentEntityId');
        $this->setAttributeCaption('ObjectClass', text(2098));

        foreach( array('FilePath','FileExt') as $attribute ) {
            $this->addAttributeGroup($attribute,'system');
        }

		foreach( array('ObjectClass','ObjectId') as $attribute ) {
			$this->addAttributeGroup($attribute,'nonbulk');
		}
 	}
 	
 	function createIterator() {
 		return new AttachmentUnifiedIterator( $this );
 	}

    function getVpds() {
        return array();
    }
}