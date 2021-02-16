<?php

class ImportXmlForm extends PMForm
{
    function getAddCaption()
    {
        return str_replace('%1', $this->getObject()->getDisplayName(), text(1722));
    }
 	
 	function getCommandClass()
 	{
 		return 'requestsimportxml';
 	}

	function getAttributes()
	{
		return array('Excel', 'object'); 	
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return text(945); 	
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return 'file'; 	

			case 'object':
				return 'custom'; 	
		}
	}

 	function getDescription( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Excel':
 				return str_replace('%1', $this->getExcelUrl(), text(50));
 		}
 	}

	function IsAttributeRequired( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return true; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}
	
	function getButtonText()
	{
		return translate('Импортировать');
	}

 	function getRedirectUrl()
	{
		return '';
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
		switch ( $attribute )
		{
			case 'object':
				echo '<input type="hidden" name="object" value="'.htmlentities($_REQUEST['object']).'">';
				break;
				
			default:
				parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
		}
	}
	
	function IsCentered()
	{
		return false;
	}
	
	function getWidth()
	{
		return '100%';
	}
	
	function IsPreviewEnabled()
	{
		return true;
	}
	
	function draw()
	{
		echo '<div style="padding-left:12px;padding-right:12px;">';
			parent::draw();
		echo '</div>';
	}

    function getExcelUrl()
    {
        $object = !is_object($this->object)
            ? getFactory()->getObject($_REQUEST['class']) : $this->object;

        if ( !is_object($object) ) return '';

        switch( $object->getClassName() )
        {
            default:
                $iterator = 'IteratorExportExcel';
                break;
        }

        return '?export=html&prepare-import&class='.$iterator.'&entity=&objects=0&show=all&caption='.IteratorBase::wintoutf8(translate($object->getDisplayName()));
    }
}