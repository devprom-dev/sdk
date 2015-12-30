<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class WikiPageMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'WikiPage' ) return;
    	
    	$object = $metadata->getObject();

		$metadata->setAttributeType('Content', 'WYSIWYG');
		$metadata->setAttributeType('ParentPage', 'REF_'.get_class($object).'Id');
		$metadata->addAttributeGroup('Author', 'nonbulk');
		$metadata->addAttributeGroup('Content', 'nonbulk');

		$metadata->addAttribute('DocumentId', 'REF_'.get_class($object).'Id', translate('Документ'), false);
		
    	$system_attributes = array( 
		        'UserField1',
		        'UserField2',
		        'UserField3',
		        'IsTemplate',
		        'IsDraft',
		        'ReferenceName',
		        'IsArchived',
		        'ContentEditor'
	    );
		        
		foreach( $system_attributes as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'system');
		}
   }
}