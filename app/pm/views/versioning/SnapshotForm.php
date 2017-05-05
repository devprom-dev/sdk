<?php

class SnapshotForm extends PMForm
{
	function __construct( $object )
	{
		parent::__construct($object);
		
		$this->getObject()->setAttributeCaption('Caption', text(1558) );
		
		$object_class = getFactory()->getClass($this->getAttributeValue('ObjectClass'));
		
		if ( is_subclass_of($object_class, 'WikiPage') )
		{
			$this->getObject()->setAttributeDescription('Caption', text(1733) );
		}
		else
		{
			$this->getObject()->setAttributeDescription('Caption', text(1560) );
		}
	}
	
 	function getAddCaption()
 	{
 		global $model_factory;
 		
		$title = text(1732);
		
		$object_class = $model_factory->getClass($this->getAttributeValue('ObjectClass'));

		if ( !class_exists($object_class) ) return;
		
		$object_it = $model_factory->getObject($object_class)->getExact($this->getAttributeValue('ObjectId'));
		
		$title .= ' "'.$object_it->object->getDisplayName().': '.$object_it->getDisplayName().'"';
		
		return $title;
 	}
 	
 	function getModifyCaption()
 	{
		return text(1139);
 	}
 	
 	function getCommandClass()
 	{
		return 'snapshotprocess';
 	}

	function getAttributes()
	{
		$attrs = parent::getAttributes();
		
		$attrs[] = 'items';
		
		return $attrs;
	}
 	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Description':
		    	return 'largetext';
		    	
			default:
				return parent::getAttributeType( $attribute );
		}
	}

	function getAttributeClass( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			default:
				return parent::getAttributeClass( $attribute );
		}
	}
	
	function getAttributeValue( $attribute )
	{
		global $_REQUEST;
		
		$value = parent::getAttributeValue( $attribute );
		
		if ( $value == '' )
		{
			$value = htmlentities($_REQUEST[$attribute], ENT_QUOTES | ENT_HTML401, APP_ENCODING); 
		}

		return $value;
	}
	
	function IsAttributeRequired( $attribute )
	{
		switch( $attribute )
		{
			case 'Caption':
				return true;
				
			default:
				return false;
		}
	}

	function IsAttributeVisible( $attribute )
	{
		switch( $attribute )
		{
			case 'Caption':
			case 'Description':
			case 'items':
				return true;
				
			default:
				return false;
		}
	}
	
	function IsAttributeModifable( $attribute )
	{
		switch( $attribute )
		{
			default:
				return true;
		}
	}

 	function drawAttribute( $attribute, $view )
	{
		switch ( $attribute )
		{
			case 'items':
				echo '<input type="hidden" name="items" value="'.$this->getAttributeValue($attribute).'">';
				echo '<input type="hidden" name="versionedclass" value="'.$this->getAttributeValue('class').'">';
				echo '<input type="hidden" name="ListName" value="'.$this->getAttributeValue('ListName').'">';
				break;

			default:
				parent::drawAttribute( $attribute, $view );
		}
	}
	
	function getWidth()
	{
		return '100%';
	}
}