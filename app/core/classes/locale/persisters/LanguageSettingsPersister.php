<?php

class LanguageSettingsPersister extends ObjectSQLPersister
{
 	function modify( $object_id, $parms )
 	{
		$settings_path = $_SERVER['DOCUMENT_ROOT'].'/settings_server.php';
		
		$language_it = getFactory()->getObject('cms_Language')->getExact( $parms['cms_LanguageId'] );
		
		$file_content = SettingsFile::setSettingValue(
			'LANG_DATEFORMAT_'.$language_it->get('CodeName'), $parms['DateFormatClass'],
				file_get_contents($settings_path) );

		file_put_contents($settings_path, $file_content);
		if ( function_exists('opcache_reset') ) opcache_reset();
 	}

 	function getSelectColumns( $alias )
 	{
 		global $_SERVER;
 		
 		$langs = array (
 			'EN' => new LanguageEnglish, 
 			'RU' => new Language
 		);
 		
 		$conditions = array();
 		
 		foreach( $langs as $key => $class )
 		{
 			if ( defined('LANG_DATEFORMAT_'.$key) ) {
 				$langs[$key] = constant( 'LANG_DATEFORMAT_'.$key );
 			}
 			else {
 				$langs[$key] = get_class($class->getDefaultDateFormat());
 			}
 			
 			array_push( $conditions, " WHEN '".$key."' THEN '".$langs[$key]."' " );
 		}
 		
 		$columns = array();
 		
 		array_push( $columns, "CASE CodeName ".
 			join($conditions, " ")." END DateFormatClass " );
 		
 		return $columns;
 	}
}