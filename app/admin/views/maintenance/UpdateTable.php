<?php

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
		return array (
			array (
				'name' => translate('Установить'),
				'url' => '?action=upload',
				'uid' => 'upload'
			),
			array(),
			array (
				'name' => translate('Скачать обновление'),
				'url' => 'http://devprom.ru/download?updates',
				'uid' => 'download'
			),
		);
	}
}
