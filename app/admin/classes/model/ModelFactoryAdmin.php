<?php

include "classes.php";

class ModelFactoryAdmin extends ModelFactoryExtended
{
	protected function buildClasses()
	{
		return array_merge( parent::buildClasses(), array(
			'pm_changerequestlinktype' => array('RequestLinkType'),
			'pm_importance' => array('Importance')
		));
	}	
}