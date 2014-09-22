<?php

class UpdateList extends PageList
{
	function IsNeedToDisplayNumber( ) { return false; }
	function IsNeedToSelect( ) { return false; }
	
 	function getIterator() 
	{
	    $it = parent::getIterator();
	    
	    $data = file_get_contents(DOCUMENT_ROOT.'conf/new-updates.json');
	    
	    if ( $data == '' ) return $it;

	    $data = JsonWrapper::decode($data);
	    
	    if ( count($data) < 1 ) return $it;

	    $rowset = $it->getRowset();
	    
	    foreach( $data as $update_info )
	    {
	        array_unshift($rowset, array(
	            'cms_UpdateId' => 0,
	            'Caption' => $update_info['version'],
	            'Description' => IteratorBase::utf8towin($update_info['description']),
	            'DownloadUrl' => $update_info['download_url']
	        ));
	    }	   

	    return $it->object->createCachedIterator($rowset);
	}
		
	function getColumns()
	{
		$this->object->setAttributeCaption('RecordCreated', translate('Дата установки'));
		
		return parent::getColumns();
	}

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'RecordCreated':

				echo $object_it->getDateTimeFormat('RecordCreated');
				
				break;
				
			case 'Description':
				
				echo $object_it->getHtml('Description');
				
				break;
	
			default:
			    
				parent::drawCell( $object_it, $attr );
		}
	}

	function IsNeedToDisplay( $attr )
	{
		return $attr == 'Caption' || $attr == 'Description' || $attr == 'RecordCreated';
	}
	
	function getItemActions( $column_name, $object_it )
	{
		$actions = array();
		
	    if ( $object_it->getId() == '0' )
	    {
	        $actions = array(
                array( 'name' => translate('Установить'), 'url' => '?action=download&parms='.$object_it->get('Caption') ),
                array(),
                array( 
                		'name' => translate('Скачать обновление'), 
                		'url' => $object_it->get('DownloadUrl'),
                		'uid' => 'download'
	        	)
	        );
	    }
    
		$plugins = getSession()->getPluginsManager();
		
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
			$plugin->interceptMethodListGetActions( $this, $actions );
		}
		
		return $actions;
	}
}
