<?php

if ( !class_exists('CheckUncheckFrame', false) ) include "CheckUncheckFrame.php";

class StoreTemplateForm extends PMForm
{
    var $sections;
    
    private $descriptions = array();

    function getAddCaption()
    {
        return text(720);
    }

    function getCommandClass()
    {
        return 'storeprojecttemplate';
    }

    function getAttributes()
    {
        global $model_factory;

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

        $attrs = parent::getAttributes();

        array_push($attrs, 'options');

        $attrs = array_merge($attrs, $this->sections);

        return $attrs;
    }

    function getName( $attribute )
    {
        switch ( $attribute )
        {
			default:
				if ( array_key_exists($attribute, $this->descriptions) )
				{
					return $this->descriptions[$attribute];
				}
				
                return parent::getName( $attribute );
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

        switch ( $attribute )
        {
            case 'Language':
                return getSession()->getProjectIt()->get('Language');
        }

        return parent::getAttributeValue( $attribute );
    }

    function IsAttributeVisible( $attribute )
    {
        if ( in_array( $attribute, $this->sections ) )
        {
            return true;
        }

        switch ( $attribute )
        {
            case 'IsDefault':
                return false;

            case 'options':
                return true;

            default:
                return parent::IsAttributeVisible( $attribute );
        }
    }

    function getAttributeType( $attribute )
    {
        if ( in_array( $attribute, $this->sections ) )
        {
            return 'char';
        }

        if ( $attribute == 'options' ) return 'custom';

        return parent::getAttributeType( $attribute );
    }

    function drawCustomAttribute( $attribute, $value, $tab_index )
    {
        switch ( $attribute )
        {
            case 'options':
                 
                echo '<label>'.text(1350).'</label>';
                 
                $frame = new CheckUncheckFrame();

                $frame->draw();

                break;

            default:
                parent::drawCustomAttribute( $attribute, $value, $tab_index );
        }
    }

    function getDescription( $attribute )
    {
        switch( $attribute )
        {
            case 'Caption':
                return text(721);

            case 'Description':
                return text(722);

            case 'FileName':
                return text(723);
        }
    }

    function isCentered()
    {
        return false;
    }
}
