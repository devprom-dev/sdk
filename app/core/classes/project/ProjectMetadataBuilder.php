<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ProjectMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Project' ) return;
    	
 		$metadata->addPersister( new ProjectVPDPersister() );

		$metadata->setAttributeType( 'Importance', 'REF_ProjectImportanceId' );

 		$metadata->setAttributeVisible( 'IsPollUsed', false );
 		$metadata->setAttributeVisible( 'StartDate', false );
 		$metadata->setAttributeVisible( 'FinishDate', false );
 		
 		$metadata->setAttributeType( 'Description', 'TEXT' );
 		
 		$metadata->setAttributeRequired( 'Budget', false );
 		$metadata->setAttributeRequired( 'Blog', false );
 		$metadata->setAttributeRequired( 'Version', false );
 		
		foreach ( array('Caption', 'CodeName') as $attribute ) {
		    $metadata->addAttributeGroup($attribute, 'tooltip');
            $metadata->addAttributeGroup($attribute, 'search-attributes');
		}
        foreach ( array('Importance', 'IsClosed') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'bulk');
        }

		$system_attributes = array(
		        'IsTender', 'Rating', 'IsPollUsed', 'Blog', 'IsBlogUsed', 
		        'StartDate', 'FinishDate', 'HasMeetings', 'IsConfigurations',
		        'Platform', 'DaysInWeek', 'WikiEditorClass', 'LinkedProject', 'Tools', 'Language'
		);
		
 		foreach ( $system_attributes as $attribute )
		{
			$metadata->addAttributeGroup($attribute, 'system');
		}
		
		$metadata->removeAttribute('HasMeetings');
    }
}