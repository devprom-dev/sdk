<?php

class BackupList extends PageList
{
	var $backup_it;
	
	function retrieve()
	{
		parent::retrieve();
		
		$backup = new Metaobject('cms_Backup');
		$this->backup_it = $backup->getAll();
		$this->backup_it->buildPositionHash( array('BackupFileName') );
	}
	
	function IsNeedToDisplayLinks( ) { return false; }

	function getColumns()
	{
		$this->object->addAttribute('Description', '', translate('Описание'), true);
		$this->object->addAttribute('Size', '', translate('Размер'), true);

		return parent::getColumns();
	}

	function IsNeedToDisplay( $attr )
	{
		switch($attr)
		{
			case 'Caption':
				return false;

			case 'RecordCreated':
			    return true;
		}

		return parent::IsNeedToDisplay($attr);
	}

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'RecordCreated':
				echo $object_it->get('RecordCreated');
				break;

			case 'Description':
				$this->backup_it->moveTo('BackupFileName', $object_it->get('Caption'));
				if( $this->backup_it->get('BackupFileName') == $object_it->get('Caption') ) {
				    parent::drawCell( $object_it, 'Caption' );
                }
				break;
				
			case 'Size':
				echo round($object_it->get('size') / 1024 / 1024, 2).' Mb';
				break;
		}
	}
	
	function getItemActions( $column_name, $object_it )
	{
		$actions = array();
		array_push( $actions, array() );
		
		array_push( $actions, array (
			'url' => '?export=download&backup_file_name='.$object_it->getId(),
			'name' => translate('Скачать'),
			'uid' => 'download'
		));
		
		array_push( $actions, array() );
		
		array_push( $actions, array (
			'url' => '?action=recoveryunpack&parms='.$object_it->get('Caption'),
			'name' => translate('Восстановить')
		));
		
		$method = new DeleteObjectWebMethod( $object_it );
		
		if ( $method->hasAccess() )
		{
		    array_push( $actions, array() );
		    array_push( $actions, array (
    			'url' => $method->getJSCall(),
    			'name' => translate('Удалить')
    		));
		}

		$plugins = getFactory()->getPluginsManager();
		
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
			$plugin->interceptMethodListGetActions( $this, $actions );
		}
		
		return $actions;
	}
}
