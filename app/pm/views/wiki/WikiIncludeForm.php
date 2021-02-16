<?php
use Devprom\ProjectBundle\Service\Wiki\WikiIncludeService;
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
        $system = $object->getAttributesByGroup('system');
 		foreach( $object->getAttributes() as $attribute => $data )
 		{
            if ( in_array($attribute, $system) ) continue;
 			$object->setAttributeVisible($attribute, false);
            $object->setAttributeRequired($attribute, false);
 		}

 		$object->addAttribute('Include', 'INTEGER', '', false, false);
        $object->setAttributeRequired('Caption', false);

 		if ( $this->getFieldValue('Include') == "1" )
 		{
 			$object->addAttribute('PageToInclude', 'REF_'.get_class($this->getObject()).'Id', $this->getObject()->getSectionName(), true, false);
 			$object->setAttributeRequired('PageToInclude', true);
 		}
 		else
 		{
 			$object->setAttributeVisible('ParentPage', true);
 			$object->setAttributeRequired('ParentPage', true);
 			$object->setAttributeCaption('ParentPage', $this->getObject()->getParentName());
 		}
    }

	function persist()
	{
	    $service = new WikiIncludeService(getFactory(), getSession());
        $service->includePagesInto($this->getObject(), $_REQUEST['PageToInclude'], $_REQUEST['ParentPage'], $_REQUEST['OrderNum']);

        if ( is_numeric($_REQUEST['ParentPage']) ) {
            $this->setObjectIt($this->getObject()->getExact($_REQUEST['ParentPage']));
        }
        else {
            $this->setObjectIt($this->getObject()->getByRef('Caption', $_REQUEST['ParentPage']));
        }
        return true;
	}

	function createFieldObject( $name )
	{
		switch ( $name )
		{		
			case 'ParentPage':
                $field = new FieldHierarchySelectorAppendable( $this->getObject()->getAttributeObject('DocumentId') );
                $field->setSystemAttribute('realtraces');
                return $field;
				
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
                $field->setSystemAttribute('realtraces');
				return $field;
				
			default:
				return parent::createFieldObject( $name );
		}
	}

	function getHint() {
        return '';
    }
}