<?php

class BackupList extends PageList
{
	private $fileSystemIt = null;
	
	function IsNeedToDisplayLinks( ) { return false; }

	function extendModel()
    {
        parent::extendModel();
        $this->getObject()->addAttribute('Size', '', translate('Размер'), true);
        $this->getObject()->setAttributeVisible('RecordCreated', true);
        $this->getObject()->setAttributeVisible('BackupFileName', true);
    }

    function buildIterator()
    {
        $this->fileSystemIt = (new BackupFileSystemRegistry($this->getObject()))->createSQLIterator("");
        $it = parent::buildIterator();

        $missedFiles = array_diff(
            $this->fileSystemIt->fieldToArray('Caption'),
            $it->fieldToArray('BackupFileName'),
            $it->fieldToArray('Caption')
        );

        foreach( $missedFiles as $fileName ) {
            $this->getObject()->setNotificationEnabled(false);
            $this->getObject()->getRegistry()->Merge(array(
                'BackupFileName' => $fileName
            ), array('BackupFileName'));
        }

        if ( count($missedFiles) > 0 ) {
            $it = parent::buildIterator();
        }
        return $it;
    }

    function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Size':
                $this->fileSystemIt->moveTo('Caption', $object_it->get('BackupFileName'));
                if ( $this->fileSystemIt->get('Caption') == '' ) {
                    if ( file_exists(SERVER_BACKUP_PATH.'devprom/') ) {
                        echo translate('В работе') . '...';
                    }
                }
                else {
                    echo round($this->fileSystemIt->get('size') / 1024 / 1024, 2).' Mb';
                }
				break;
            default:
                parent::drawCell( $object_it, $attr );
		}
	}
	
	function getItemActions( $column_name, $object_it )
	{
		$actions = array();
		array_push( $actions, array() );
		
		array_push( $actions, array (
			'url' => '?export=download&backup='.$object_it->getId(),
			'name' => translate('Скачать'),
			'uid' => 'download'
		));
		
		array_push( $actions, array() );
		
		array_push( $actions, array (
			'url' => '?action=recoveryunpack&parms='.$object_it->get('BackupFileName'),
			'name' => translate('Восстановить')
		));
		
		$method = new DeleteObjectWebMethod( $object_it );
		
		if ( $method->hasAccess() ) {
		    array_push( $actions, array() );
		    array_push( $actions, array (
    			'url' => $method->getJSCall(),
    			'name' => translate('Удалить')
    		));
		}

		$plugins = getFactory()->getPluginsManager();
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
		foreach( $plugins_interceptors as $plugin ) {
			$plugin->interceptMethodListGetActions( $this, $actions );
		}
		
		return $actions;
	}
}
