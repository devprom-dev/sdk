<?php
include SERVER_ROOT_PATH."admin/classes/templates/validators/SystemTemplateYamlValidator.php";

class SystemTemplateForm extends AdminPageForm
{
	function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        foreach( $object->getAttributes() as $attribute => $data ) {
            $object->setAttributeVisible($attribute, false);
        }
        $object->setAttributeVisible('Content', true);
    }

	function getValidators()
    {
        return array_merge(
            parent::getValidators(),
            array(
                new SystemTemplateYamlValidator()
            )
        );
    }

    function createField( $attr )
	{
		$field = parent::createField($attr);
		
		switch($attr)
		{
		    case 'Content':
		    	$field->setRows(40);
				$field->setWrap(false);
		    	break;
		}
		
		return $field;
	}
	
	function getDeleteActions($objectIt)
	{
	    $actions = array();
	    if ( !is_object($objectIt) ) return $actions;
	    
		$method = new DeleteObjectWebMethod($objectIt);
		if ( $method->hasAccess() && file_exists($objectIt->getFilePath()) ) {
		    $actions[] = array(
			    'name' => text(2039), 'url' => $method->getJSCall() 
		    );
		}
		
		return $actions;
	}

	function getFieldDescription($field_name)
    {
        switch( $field_name ) {
            case 'Content':
                $docsUrl = \EnvironmentSettings::getHelpDocsUrl();
                if ( $docsUrl != '' ) {
                    $docsUrl = str_replace(basename($docsUrl), '4651.html#4766', $docsUrl);
                    return sprintf(text(3017), $docsUrl);
                }
                break;
            default:
                return parent::getFieldDescription($field_name);
        }
    }
}