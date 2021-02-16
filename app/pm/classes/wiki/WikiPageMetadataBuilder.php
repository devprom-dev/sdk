<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/WikiPageDependencyPersister.php";
include_once "persisters/WikiPageDetailsPersister.php";
include_once 'persisters/DocumentVersionPersister.php';
include_once "persisters/WikiPageModifierPersister.php";
include_once "persisters/WikiPageIsIncludedPersister.php";
include "persisters/WikiPageFeaturePersister.php";
include "persisters/WikiPageFeatureTracesPersister.php";

class WikiPageMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof WikiPage) return;
    	
    	$object = $metadata->getObject();

		$metadata->setAttributeType('Content', 'WYSIWYG');
        $metadata->setAttributeCaption('Content', translate('Текст'));
        $metadata->setAttributeOrderNum('Content', 30);
		$metadata->setAttributeType('ParentPage', 'REF_'.get_class($object).'Id');
		$metadata->addAttributeGroup('Author', 'nonbulk');
		$metadata->addAttributeGroup('Content', 'nonbulk');
		$metadata->setAttributeOrderNum('PageType', 12);
        $metadata->setAttributeVisible('PageType', true);
        $metadata->addAttributeGroup('PageType', 'type');

		$metadata->addAttribute('DocumentId', 'REF_'.get_class($object).'Id', $object->getDocumentName(), false);
        $metadata->setAttributeEditable('DocumentId', false);

        $metadata->addAttribute('Modifier', 'REF_UserId', translate('Изменил'), false);
        $metadata->setAttributeEditable('Modifier', false);
        $metadata->addPersister( new WikiPageModifierPersister() );

		foreach( array('DocumentId','ParentPage') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'skip-network');
		}
        foreach( array('ParentPage') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'skip-chart');
        }

		$metadata->addAttributeGroup('Dependency', 'trace');
		$metadata->setAttributeDescription('Dependency', text(2131));
        $metadata->addAttributeGroup('Dependency', 'trace-reuse');
        $metadata->setAttributeEditable('Dependency', false);
		$metadata->addPersister( new WikiPageDependencyPersister() );
		$metadata->addPersister( new WikiPageDetailsPersister() );

		$metadata->setAttributeEditable('SectionNumber', false);

        $metadata->addAttribute('IncludedIn', 'REF_'.get_class($object).'Id', translate('Включено в'), true);
        $metadata->addAttributeGroup('IncludedIn', 'trace');
        $metadata->addAttributeGroup('IncludedIn', 'trace-reuse');
        $metadata->setAttributeEditable('IncludedIn', false);
        $metadata->addPersister( new WikiPageIsIncludedPersister() );

        $metadata->addAttribute('DocumentVersion', 'VARCHAR', translate('Бейзлайн'), true, true, '', 40);
        $metadata->addPersister( new DocumentVersionPersister() );

        if ( $object instanceof ProjectPage ) {
            $metadata->addAttributeGroup('DocumentVersion', 'system');
            $metadata->setAttributeVisible('DocumentVersion', false);
        }
        else {
            $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
            if( $methodology_it->HasFeatures() && getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Feature')) )
            {
                $metadata->addAttribute( 'Feature', 'REF_pm_FunctionId', translate('Функции'), true, false);
                $metadata->addPersister( new WikiPageFeaturePersister() );
                $metadata->addAttributeGroup('Feature', 'bulk');
                $metadata->addAttributeGroup('Feature', 'additional');
                $metadata->addAttributeGroup('Feature', 'trace-vertical');

                $metadata->addAttribute( 'FeatureIssues', 'REF_RequestId', text(3010), false, false);
                $metadata->addPersister( new WikiPageFeatureTracesPersister() );
            }
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
            'ParentPath',
            'IsDocument'
        );
        foreach( $system_attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
        }
        foreach ( array('Caption','DocumentVersion') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'tooltip');
        }
        foreach ( array('ParentPage', 'Content', 'DocumentId','SectionNumber','PageType') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'skip-tooltip');
        }
        $metadata->addAttributeGroup('ParentPage', 'hierarchy-parent');
	}
}