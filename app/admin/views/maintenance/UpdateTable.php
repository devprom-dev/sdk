<?php
include_once SERVER_ROOT_PATH."admin/classes/checkpoints/CheckpointSupportPayed.php";
include('UpdateList.php');

class UpdateTable extends StaticPageTable
{
	function getList()
	{
		return new UpdateList( $this->object );
	}

	function getCaption() 
	{
		return '';
	}

	function drawFilter()
	{
	}
	
	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' )
		{
			return 'RecordCreated.D';
		}
		
		return parent::getSortDefault( $sort_parm );
	}
	
	function getActions()
	{
		return array();
	}
	
	function getNewActions()
	{
		$actions = array();

		$data = file_get_contents(DOCUMENT_ROOT.CheckpointSupportPayed::UPDATES_FILE);
		if ( $data != '' ) {
			$data = CheckpointUpdatesAvailable::getNewUpdatesOnly(JsonWrapper::decode($data));
			if ( count($data) > 0 ) {
				$actions[] = array (
					'name' => translate('Скачать обновление'),
					'url' => 'javascript: downloadUpdate()',
					'uid' => 'download'
				);
			}
		}
		if ( getFactory()->getAccessPolicy()->can_create($this->getObject()) ) {
			$actions[] = array();
			$actions[] = array (
				'name' => translate('Установить'),
				'url' => '?action=upload',
				'uid' => 'upload'
			);
		}
		return $actions;
	}
}
