<?php

class ArtefactForm extends PMPageForm
{
 	function IsAttributeVisible( $attr_name ) 
 	{
		switch ( $attr_name )
		{
			case 'Version':
				return getSession()->getProjectIt()->getMethodologyIt()->HasVersions();
				
			default:
				return parent::IsAttributeVisible( $attr_name );
		}
	}
	
 	function validateInputValues( $id, $action )
	{
		if ( $action == 'add' || $action == 'modify' )
		{
			$used_by_artefacts = 0;
			
			$artefact_it = $this->getObject()->getAll();
			
			while( !$artefact_it->end() )
			{
				if ( $artefact_it->getId() != $id ) $used_by_artefacts += $artefact_it->getFileSizeKb('Content');
				
				$artefact_it->moveNext();
			}

			$used_by_artefacts += $_FILES['Content']['size'] / 1024;
			
			$configuration = getConfiguration();
			
			if ( $configuration->exceedMaxArtefactsVolume($used_by_artefacts) ) 
			{
				return text('fileserver4');
			}
		}
		
		return parent::validateInputValues( $id, $action );
	}
	
	function createFieldObject( $name ) 
	{
		if ( $name == 'Version' ) 
		{
			return new FieldAutoCompleteObject( getFactory()->getObject('Version') );
		}
		
		return parent::createFieldObject($name);
	}
}