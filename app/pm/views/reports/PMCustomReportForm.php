<?php

class PMCustomReportForm extends PMPageForm
{
	function __construct() 
	{
		parent::__construct( getFactory()->getObject('pm_CustomReport') );
	}

 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch($attr_name) 
 		{
 		    case 'ReportBase':
 		    case 'Category':
 		    case 'Url':
 		    case 'OrderNum':
 		    case 'Author':
 		    	return false;

 		    case 'IsHandAccess':
 		    	return !is_object($this->getObjectIt());
 		    	
 		    default:
				return parent::IsAttributeVisible( $attr_name );
 		}
	}

	function createFieldObject( $attr_name ) 
	{
		switch ( $attr_name )
		{
			case 'ReportBase':
				
				$report = getFactory()->getObject('PMReport');
				$report->setSystemOnly();
				
				return new FieldDictionary( $report );

			default:
				return parent::createFieldObject( $attr_name );
		}
	}
	
	function getFieldValue( $attr )
	{
		switch ( $attr )
	    {
	        case 'IsHandAccess':
	        	
	        	if ( !is_object($this->getObjectIt()) ) return "Y";
	        	
	        	break;
	    }
		
		$value = parent::getFieldValue($attr);
	    
	    if ( $value != "" ) return $value;
	    
	    switch ( $attr )
	    {
	        case 'Caption':
	        	
	        	if ( parent::getFieldValue('ReportBase') != '' )
	        	{
	        		return getFactory()->getObject('PMReport')->getExact(parent::getFieldValue('ReportBase'))->getDisplayName();
	        	}
	        	elseif ( parent::getFieldValue('Module') != '' )
	        	{
	        		return getFactory()->getObject('Module')->getExact(parent::getFieldValue('Module'))->getDisplayName();
	        	}
	        	else
	        	{
	        		return parent::getFieldValue('Caption');
	        	}	        		
	        		            
	        case 'Url':
	        	
	    	    if ( parent::getFieldValue('ReportBase') != '' )
	        	{
	        		return getFactory()->getObject('PMReport')->getExact(parent::getFieldValue('ReportBase'))->get('QueryString');
	        	}
	        	elseif ( parent::getFieldValue('Module') != '' )
	        	{
	        		return getFactory()->getObject('Module')->getExact(parent::getFieldValue('Module'))->get('QueryString');
	        	}
	    		else
	        	{
	        		return parent::getFieldValue('Url');
	        	}

	    	case 'Category':
	        	
	        	if ( parent::getFieldValue('ReportBase') != '' )
	        	{
	        		return getFactory()->getObject('PMReport')->getExact(parent::getFieldValue('ReportBase'))->get('Category');
	        	}
	        	elseif ( parent::getFieldValue('Module') != '' )
	        	{
	        		return getFactory()->getObject('Module')->getExact(parent::getFieldValue('Module'))->get('Category');
	        	}
	        	else
	        	{
	        		return parent::getFieldValue('Category');
	        	}	        		
	        	
	        case 'IsHandAccess': return "Y";
	        	
	        default: return $value;
	    }
	}
	
	function getFieldDescription( $attr )
	{
		switch ( $attr )
		{
			case 'IsHandAccess':
				return text(1831);
				
			default:
				return parent::getFieldDescription( $attr );
		}
	}

	function redirectOnAdded( $object_it, $redirect_url = '' ) 
	{
	    $report_it = getFactory()->getObject('PMReport')->getExact($object_it->getId());
	    $item = $report_it->buildMenuItem();
	    exit(header('Location: '.$item['url']));
	}
}