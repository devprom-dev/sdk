<?php
include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorsDictionary.php";
include "FieldRestUISettings.php";

class ProjectForm extends SettingsFormBase
{
    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();

        foreach( array('FinishDate') as $attribute ) {
            $object->setAttributeEditable($attribute, false);
        }
        foreach( array('Features', 'SpentHours', 'SpentHoursWeek') as $attribute ) {
            $object->setAttributeVisible($attribute, false);
        }

        $project_roles = getSession()->getRoles();
        if ( $project_roles['lead'] ) {
            $object->addAttribute('ModuleSettings', '', text(1906), true, false, text(1911));
        }
        $object->addAttribute('ProjectKey', 'VARCHAR', text(2287), true, false, text(2288));
        $object->setAttributeEditable('ProjectKey', false);
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

    function getBodyTemplate() {
        return "core/PageFormBody.php";
    }

    function getRedirectUrl() {
		return '/pm/'.$this->getObjectIt()->get('CodeName').'/project/settings';
	}
    
    function getShortAttributes() {
        return array(
            'CodeName', 'StartDate', 'FinishDate', 'Importance', 'Language'
        );
    }

    function getPageTitle() {
        return text(2618);
    }
}