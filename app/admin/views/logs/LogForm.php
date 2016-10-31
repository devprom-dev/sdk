<?php

class LogForm extends AdminForm
{
    function getModifyCaption()
    {
        return str_replace('%1', $this->getObjectIt()->getDisplayName(), text(1712));
    }

    function getAttributes()
    {
    	return array( 'Caption' );
    }
    
    function getAttributeType( $attribute )
    {
    	return 'custom';
    }
    
 	function getRedirectUrl()
	{
		return '/admin/log/';
	}
    
    function getFormUrl()
	{
		return '/admin/log/';
	}
	
	function getAttributeValue( $attribute )
	{
		return $this->getFileTail( SERVER_LOGS_PATH.'/'.$this->getObjectIt()->get('Caption') ); 
	}
	
	function drawCustomAttribute( $attribute, $value, $index )
	{
		echo '<textarea rows=40 style="width:100%;overflow:scroll;">'.$value.'</textarea>';
	}
	
	private function getFileTail( $file_path, $lines = 1820 )
	{
		$fp = fopen($file_path, 'r');

		if ( $fp === false ) return '';
		
		$pos = -1; $line = ''; $c = '';
		
		$passed = 0;
		
		do {
		    $line = $c . $line;
		    
		    if ( fseek($fp, $pos--, SEEK_END) < 0 ) break;
		    
		    $c = fgetc($fp);
		    
		    if ( $c === false ) break; 
		    
		    if ( $c == chr(10) || $c == chr(13) ) $passed++;
		}
		while ($passed < $lines);
		
		fclose($fp);		
		
		return $line;
	}
	
	function getRenderParms()
	{
		return array_merge( parent::getRenderParms($parms), 
				array (
						'buttons_template' => ''
				)
		);
	}
}