<?php

class ImportXmlSection extends InfoSection
{
    var $object;
    
    function __construct( $object )
    {
        $this->object = $object;
    }
    
 	function getCaption() 
 	{
 		return translate('Описание');
 	}
 	
 	function getExcelUrl()
 	{
 	 	$object = !is_object($this->object) 
 		    ? getFactory()->getObject($_REQUEST['class']) : $this->object;
 		
 		if ( !is_object($object) ) return ''; 
 		
		switch( $object->getClassName() )
		{
			case 'WikiPage':
				$iterator = 'WikiIteratorExportExcelText';
				break;

			case 'pm_ChangeRequest':
				$iterator = 'IteratorExportExcel';
				break;

			default:
				$iterator = 'IteratorExportExcel';
				break;
		}
 		
 		return '?export=html&class='.$iterator.'&entity=&objects=0&caption='.IteratorBase::wintoutf8(translate($object->getDisplayName()));
 	}
 	
 	function drawBody()
 	{
 		echo '1. '.str_replace('%1', $this->getExcelUrl(), text(50));

 		echo '<br/><br/>';
 		
 		echo '2. '.text(378);
 	}
}