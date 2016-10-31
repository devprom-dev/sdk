<?php

include_once SERVER_ROOT_PATH."cms/c_iterator_file.php";

class CheckpointBackups extends CheckpointEntryDynamic
{
	function execute()
	{
		$object = getFactory()->getObject('SystemSettings');
		
		$it = new IteratorFile($object, SERVER_BACKUP_PATH, "zip");
		
		if ( $it->count() < 1 )
		{
			// check when the system has been installed
			$days_it = $object->createSQLIterator(" SELECT TO_DAYS(NOW()) - TO_DAYS(t.RecordCreated) Days FROM cms_User t ORDER BY t.RecordCreated");

			$this->setValue( $days_it->get('Days') > 1 ? "0" : "1");
		}
		else
		{
			$it->sortCreatedDesc();
			
			$days_it = $object->createSQLIterator(" SELECT TO_DAYS(NOW()) - TO_DAYS('".date("Y-m-d", $it->get('ctime'))."') Days");

			$this->setValue( $days_it->get('Days') > 1 ? "0" : "1");
		}
	}
	
	function getTitle()
	{
		return text(1858);
	}
	
	function getDescription()
	{
		return text(1859);
	}

    function getWarning()
    {
        return text(2260);
    }
}
