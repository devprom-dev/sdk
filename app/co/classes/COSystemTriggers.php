<?php

class COSystemTriggers extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		switch( $object_it->object->getEntityRefName() )
		{
			case 'co_ProjectGroup':
			case 'co_ProjectGroupLink':
                foreach( array('projects', 'sessions') as $path ) {
                    getFactory()->getCacheService()->invalidate($path);
                }
				break;
		}
	}
}
