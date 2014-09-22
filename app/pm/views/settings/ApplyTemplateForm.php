<?php

if ( !class_exists('CheckUncheckFrame', false) ) include "CheckUncheckFrame.php";

class ApplyTemplateForm extends PMForm
{
    var $sections;
    
    private $descriptions = array();

    function getAddCaption()
    {
        return text(727);
    }

    function getCommandClass()
    {
        return 'importprojecttemplate';
    }

    function getAttributes()
    {
        global $model_factory;

        $attrs = array();

        array_push( $attrs, 'ProjectTemplate', 'options' );

        $this->sections = array();

        $section = $model_factory->getObject('ProjectTemplateSections');

        $section_it = $section->getAll();

        while ( !$section_it->end() )
        {
            if ( $section_it->get('IsVisible') == 'Y' )
            {
                $this->sections[] = $section_it->get('ReferenceName');
                $this->descriptions[$section_it->get('ReferenceName')] = $section_it->get('Description');
            }
            	
            $section_it->moveNext();
        }

        $attrs = array_merge( $attrs, $this->sections );

        return $attrs;
    }

    function getName( $attribute )
    {
        switch ( $attribute )
        {
            case 'ProjectTemplate':
                return text(728);
                
            default:
            	return $this->descriptions[$attribute];
        }
    }

    function getAttributeValue( $attribute )
    {
    	if ( $attribute == 'ProjectArtefacts' )
    	{
    		return 'N';
    	}
    		
        if ( in_array( $attribute, $this->sections ) )
        {
            return 'Y';
        }
    }

    function IsAttributeVisible( $attribute )
    {
        if ( in_array( $attribute, $this->sections ) )
        {
            return true;
        }

        switch ( $attribute )
        {
            case 'ProjectTemplate':
            case 'options':
                return true;
        }
    }

    function getAttributeType( $attribute )
    {
        if ( in_array( $attribute, $this->sections ) )
        {
            return 'char';
        }

        if ( $attribute == 'options' ) return 'custom';

        switch ( $attribute )
        {
            case 'ProjectTemplate':
                return 'object';
        }
    }

    function getAttributeClass( $attribute )
    {
        global $model_factory;

        switch ( $attribute )
        {
            case 'ProjectTemplate':
                return $model_factory->getObject('pm_ProjectTemplate');
        }
    }

    function drawCustomAttribute( $attribute, $value, $tab )
    {
        switch ( $attribute )
        {
            case 'options':
                 
                echo '<label>'.text(1351).'</label>';
                 
                $frame = new CheckUncheckFrame();

                $frame->draw();

                break;

            default:
                parent::drawCustomAttribute( $attribute, $value, $tab );
        }
    }

    function getDescription( $attribute )
    {
        switch( $attribute )
        {
            case 'ProjectTemplate':
                return text(729);
        }
    }

    function isCentered()
    {
        return false;
    }

    function getButtonText()
    {
        return text(730);
    }
}