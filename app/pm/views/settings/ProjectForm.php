<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorsDictionary.php";

class ProjectForm extends PMPageForm
{
    function __construct( $project_it )
    {
    	$this->setObjectIt( $project_it );
    	
        parent::__construct( $project_it->object );
    }

    function validateInputValues( $id, $action )
    {
        global $_REQUEST;

        $message = parent::validateInputValues( $id, $action );

        if ( $message != '' )
        {
            return $message;
        }

        // check for uniqueness of project code name
        $it = $this->object->getByRefArray(
                array('CodeName' => $_REQUEST['CodeName'] )
        );
        	
        if ( $it->count() > 0 && $it->getId() != $id )
        {
            return translate('ѕроект с таким кодовым названием уже зарегистрирован, укажите другое кодовое название вашего проекта');
        }

        // check for correctness of project code name
        if ( !$this->object->validCodeName($_REQUEST['CodeName']) )
        {
            return translate('¬ кодовом названии проекта можно использовать только латинские буквы, цифры и символы "-" и "_", например, myproject_0');
        }

        return '';
    }

    function IsNeedButtonNew() {
        return false;
    }

    function IsNeedButtonCopy() {
        return false;
    }

    function IsNeedButtonDelete() {
        return false;
    }

    function IsNeedButtonSave() {
        return true;
    }

    function IsAttributeVisible( $attr_name )
    {
        $visible = array(
		        'IsKnowledgeUsed', 'IsBlogUsed', 'IsFileServer', 
		        'IsSubversionUsed', 'DaysInWeek', 'WikiEditorClass', 'Language'
		);

        return in_array($attr_name, $visible) ? true : parent::IsAttributeVisible( $attr_name ); 
    }

    function getFieldDescription( $name )
    {
        switch ( $name )
        {
            case 'IsClosed':
                return text(663);

            case 'IsPollUsed':
                return text(36);

            case 'IsKnowledgeUsed':
                return text(678);

            case 'IsBlogUsed':
                return text(679);

            case 'DaysInWeek':
                return text(1023);

            default:
                return parent::getFieldDescription( $name );
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
        global $model_factory;

        $actions = array();

        $project = $model_factory->getObject('pm_Project');

        if ( !getFactory()->getAccessPolicy()->can_modify($project) ) return $actions;

        array_push( $actions,
        array( 'name' => text(718), 'url' => '?action=applytemplate' )
        );
        	
        array_push( $actions,
        array( 'name' => text(719), 'url' => '?action=newtemplate' )
        );

        return $actions;
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
                'uid_icon' => ''
        ));
    }
}