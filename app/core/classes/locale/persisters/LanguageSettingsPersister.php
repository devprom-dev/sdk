<?php

class LanguageSettingsPersister extends ObjectSQLPersister
{
 	function modify( $object_id, $parms )
 	{
 		global $model_factory;
 		
		$settings_path = $_SERVER['DOCUMENT_ROOT'].'/settings_server.php';
		
		$file = fopen($settings_path, 'r', 1);

		$file_content = fread($file, filesize($settings_path));
		
		fclose($file);

		$language = $model_factory->getObject('cms_Language');
		
		$language_it = $language->getExact( $parms['cms_LanguageId'] );
		
		$file_content = SettingsFile::setSettingValue(
			'LANG_DATEFORMAT_'.$language_it->get('CodeName'), $parms['DateFormatClass'], $file_content );
		
		$file = fopen($settings_path, 'w', 1);
		
		fwrite( $file, $file_content );
		fclose( $file );
		
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