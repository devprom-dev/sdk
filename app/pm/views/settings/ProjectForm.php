<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorsDictionary.php";

class ProjectForm extends PMPageForm
{
    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        $object->setAttributeCaption('Rating', translate('Скорость'));
        $object->setAttributeEditable('Rating', false);
        $object->setAttributeVisible('Rating', true);
        $object->setAttributeOrderNum('Rating', 300);
        $object->setAttributeDescription('Rating', text(2284));

        $object->addAttribute('ProjectKey', 'VARCHAR', text(2287), true, false, text(2288), 310);
        $object->setAttributeEditable('ProjectKey', false);
    }

    function buildModelValidator()
 	{
 		$validator = parent::buildModelValidator();
 		
 		$validator->addValidator( new ModelValidatorProjectCodeName() );
 		$validator->addValidator( new ModelValidatorUnique(array('CodeName')) );
 		
 		return $validator;
 	}
 	
    function IsNeedButtonDelete()
    {
        return false;
    }

    function IsAttributeVisible( $attr_name )
    {
        $visible = array(
		        'DaysInWeek', 'WikiEditorClass', 'Language'
		);

        return in_array($attr_name, $visible) ? true : parent::IsAttributeVisible( $attr_name ); 
    }

    function getFieldDescription( $name )
    {
        switch ( $name )
        {
            case 'IsClosed':
                return text(663);

            case 'DaysInWeek':
                return text(1023);

            default:
                return parent::getFieldDescription( $name );
        }
    }

    function getFieldValue($field)
    {
        switch( $field ) {
            case 'ProjectKey':
                return $this->getObjectIt()->getPublicKey();
            default:
                return parent::getFieldValue($field);
        }
    }

    function createFieldObject( $attr )
    {
        switch ( $attr )
        {
            case 'WikiEditorClass':
                return new WikiEditorsDictionary();

            default:
                return parent::createFieldObject( $attr );
        }
    }

    function getActions()
    {
        return array();
    }

    function getPageTitle()
    {
        return '';
    }

    function getRedirectUrl()
	{
		return '/pm/'.$this->getObjectIt()->get('CodeName').'/project/settings';
	}
    
    function getRenderParms()
    {
        return array_merge( parent::getRenderParms(), array (
            'uid_icon' => '',
            'uid' => ''
        ));
    }
}