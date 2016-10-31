<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include "persisters/WikiPageStylePersister.php";
include "persisters/WikiPageDependencyPersister.php";
include "persisters/WikiIncludePagePersister.php";
include "persisters/WikiPageDetailsPersister.php";


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
		$metadata->setAttributeOrderNum('PageType', 12);
		$metadata->addAttribute('DocumentId', 'REF_'.get_class($object).'Id', $object->getDocumentName(), false);
		$metadata->addAttributeGroup('UID', 'alternative-key');

		foreach( array('DocumentId','ParentPage') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'skip-network');
		}

		$metadata->addAttributeGroup('Dependency', 'trace');
		$metadata->setAttributeDescription('Dependency', text(2131));
		$metadata->addPersister( new WikiIncludePagePersister() );
		$metadata->addPersister( new WikiPageDependencyPersister() );

    	$system_attributes = array( 
		        'UserField1',
		        'UserField2',
		        'UserField3',
		        'IsTemplate',
		        'IsDraft',
		        'ReferenceName',
		        'IsArchived',
		        'ContentEditor',
				'Includes'
	    );
		        
		foreach( $system_attributes as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'system');
		}
		foreach ( array('Content','DocumentId','Author') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'tooltip');
		}

		$metadata->addPersister(new WikiPageStylePersister());
		$metadata->addPersister(new WikiPageDetailsPersister());
	}
}