<?php
include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorsDictionary.php";
include "FieldRestUISettings.php";

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
        $object->setAttributeEditable('FinishDate', false);

        $project_roles = getSession()->getRoles();
        if ( $project_roles['lead'] ) {
            $object->addAttribute('ModuleSettings', '', text(1906), true, false, text(1911));
        }
        $object->addAttribute('ProjectKey', 'VARCHAR', text(2287), true, false, text(2288));
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
            'DaysInWeek', 'WikiEditorClass', 'Language', 'StartDate', 'FinishDate', 'ModuleSettings'
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

            case 'FinishDate':
                return str_replace('%1', getFactory()->getObject('Module')->getExact('project-plan-hierarchy')->getUrl(), text(2474));

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

            case 'ModuleSettings':
                return new FieldRestUISettings(getSession()->getApplicationUrl().'settings/modules');

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

    function getShortAttributes()
    {
        return array(
            'CodeName', 'StartDate', 'FinishDate', 'Importance', 'Language'
        );
    }
}