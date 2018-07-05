<?php
include_once "PMWikiForm.php";

class WikiIncludeForm extends PMPageForm
{
 	function __construct( $object ) 
	{
		parent::__construct( $object );
	}
    protected function extendModel()
    {
 		parent::extendModel();
 		
 		$object = $this->getObject();
 		foreach( $object->getAttributes() as $attribute => $data )
 		{
 			$object->setAttributeVisible($attribute, false);
 			$object->setAttributeRequired($attribute, false);
 		}
 		$object->addAttribute('Include', 'INTEGER', '', false, false);
 		
 		if ( $this->getFieldValue('Include') == "1" )
 		{
 			$object->addAttribute('PageToInclude', 'REF_'.get_class($this->getObject()).'Id', text('testing55'), true, false, text('testing59'));
 			$object->setAttributeRequired('PageToInclude', true);
 		}
 		else
 		{
 			$object->setAttributeVisible('ParentPage', true);
 			$object->setAttributeRequired('ParentPage', true);
 			$object->setAttributeCaption('ParentPage', $this->getObject()->getDocumentName());
 		}
    }

	function persist()
	{
		if ( !parent::persist() ) return false;

		if ( $this->getFieldValue('Include') == "1" ) {
		}
		else {
			// redirect to document's page
			$object_it = $this->getObjectIt();
			$this->setObjectIt($object_it->getRef('DocumentId'));
		}
		return true;
	}

	function createFieldObject( $name )
	{
		switch ( $name )
		{		
			case 'ParentPage':
				return new FieldHierarchySelectorAppendable( $this->getObject()->getAttributeObject('DocumentId') );
				
			case 'PageToInclude':
				$treeObject = $this->getObject()->getAttributeObject($name);
				if ( $treeObject instanceof TestScenario ) {
					$searchObject = new TestScenarioOnly();
				}
				else {
					$searchObject = $treeObject;
				}
				$field = new FieldHierarchySelector($searchObject);
				$field->setTreeObject($treeObject);
				return $field;
				
			default:
				return parent::createFieldObject( $name );
		}
	}    
}