<?php

class VersionSettingsForm extends PMPageForm
{
    function VersionSettingsForm()
    {
        global $model_factory;

        parent::PMPageForm(
                $model_factory->getObject('pm_VersionSettings') );
    }

    function getCaption()
    {
        return '';
    }

    function IsNeedButtonNew()
    {
        return false;
    }

    function IsNeedButtonDelete()
    {
        return false;
    }

    function IsNeedButtonCopy()
    {
        return false;
    }

    function IsAttributeVisible( $attr_name )
    {
        switch( $attr_name )
        {
            case 'UseIteration':
                return getSession()->getProjectIt()->getMethodologyIt()->HasPlanning();
                	
            case 'Project':
                return false;
                	
            default:
                return parent::IsAttributeVisible( $attr_name );
        }
        	
        if( $attr_name == 'Project') return false;
        	
    }

    function getFieldDescription( $name )
    {
        switch ( $name )
        {
            case 'UseRelease':
                return text(655);

            case 'UseIteration':
                return text(656);

            case 'UseBuild':
                return text(657);
        }
    }
    
    function getRenderParms()
    {
        return array_merge( parent::getRenderParms(), array (
            'uid_icon' => '',
            'caption' => ''
        ));
    }
}
