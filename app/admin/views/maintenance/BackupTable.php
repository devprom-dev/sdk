<?php
include 'BackupList.php';

class BackupTable extends PageTable
{
	function getList() {
		return new BackupList( $this->object );
	}

	function drawFilter()
	{
	}
	
	function getActions() {
		return array();
	}
	
	function getNewActions()
	{
		$actions = array (
			array (
				'name' => text(1302),
				'url' => '?action=backupdatabase'
			),
			array()
		);

		return $actions;
	}

	function getCaption() {
        return translate('Резервные копии');
    }

    function getSortDefault($sort_parm = 'sort') {
        return 'RecordCreated.D';
    }
}
