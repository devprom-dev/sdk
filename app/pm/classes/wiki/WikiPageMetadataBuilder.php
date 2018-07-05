<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/WikiPageStylePersister.php";
include_once "persisters/WikiPageDependencyPersister.php";
include_once "persisters/WikiIncludePagePersister.php";
include_once "persisters/WikiPageDetailsPersister.php";
include_once 'persisters/DocumentVersionPersister.php';
include_once "persisters/WikiPageModifierPersister.php";

class WikiPageMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'WikiPage' ) return;
    	
    	$object = $metadata->getObject();

		$metadata->setAttributeType('Content', 'WYSIWYG');
        $metadata->setAttributeOrderNum('Content', 30);
		$metadata->setAttributeType('ParentPage', 'REF_'.get_class($object).'Id');
		$metadata->addAttributeGroup('Author', 'nonbulk');
		$metadata->addAttributeGroup('Content', 'nonbulk');
		$metadata->setAttributeOrderNum('PageType', 12);
		$metadata->addAttribute('DocumentId', 'REF_'.get_class($object).'Id', $object->getDocumentName(), false);
		$metadata->addAttributeGroup('UID', 'alternative-key');

        $metadata->addAttribute('Modifier', 'REF_UserId', translate('Изменил'), false);
        $metadata->addPersister( new WikiPageModifierPersister() );

		foreach( array('DocumentId','ParentPage') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'skip-network');
		}

		$metadata->addAttributeGroup('Dependency', 'trace');
		$metadata->setAttributeDescription('Dependency', text(2131));
		$metadata->addPersister( new WikiIncludePagePersister() );
		$metadata->addPersister( new WikiPageDependencyPersister() );
		$metadata->addPersister( new WikiPageStylePersister() );
		$metadata->addPersister( new WikiPageDetailsPersister() );

        if ( !$metadata->getObject() instanceof Requirement ) {
            foreach ( array('Estimation','Importance') as $attribute ) {
                $metadata->addAttributeGroup($attribute, 'system');
            }
        }
        if ( $metadata->getObject() instanceof ProjectPage ) {
            foreach ( array('State') as $attribute ) {
                $metadata->removeAttribute($attribute);
            }
        }
        else {
            $metadata->addAttribute('DocumentVersion', 'VARCHAR', translate('Бейзлайн'), false, false, '', 40);
            $metadata->addPersister( new DocumentVersionPersister() );
        }

        $system_attributes = array(
            'UserField1',
            'UserField2',
            'UserField3',
            'IsTemplate',
            'IsDraft',
            'ReferenceName',
            'IsArchived',
            'ContentEditor',
            'Includes',
            'SectionNumber',
            'ParentPath'
        );
        foreach( $system_attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
        }
        foreach ( array('Content','DocumentId','Author','Caption','DocumentVersion') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'tooltip');
        }
	}
}