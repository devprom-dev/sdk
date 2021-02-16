<?php

include SERVER_ROOT_PATH . "admin/classes/model/classes.php";

class ModelFactoryAdmin extends ModelFactoryExtended
{
	protected function buildClasses()
	{
		return array_merge( parent::buildClasses(), array(
			'pm_changerequestlinktype' => array('RequestLinkType'),
			'pm_importance' => array('Importance'),
			'cms_blacklist' => array('BlackList'),
            'cms_backup' => array('Backup')
		));
	}	
}