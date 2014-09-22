<?php
 
 class ActivateUser
 {
 	function execute()
	{
		global $_REQUEST, $model_factory;

		$key = $_REQUEST['key'];

		if( !isset($key) ) 
		{
			exit(header('Location: /profile'));
		}

		// определим участника для которого сбрасывается пароль
		$is_valid_key = false;
		$part_cls = $model_factory->getObject('cms_User');
		
		$part_it = $part_cls->getByRefArray(
			array( 'IsActivated' => 'N' ) );
		
		for($i = 0; $i < $part_it->count(); $i++) 
		{
			if( $key == $part_it->getActivationKey() && !$part_it->IsActivated() ) 
			{
				$is_valid_key = true;
				
				$part_it->modify( array('IsActivated' => 'Y') );
				
				$session = getSession();
				$session->open( $part_it );
				
				break;
			}
			
			$part_it->moveNext();
		}
		
		if ( $is_valid_key )
		{
			exit(header('Location: /co/activated.php?user='.$part_it->getId()));
		}
		else
		{
			exit(header('Location: /profile'));
		}
	}
 }
 
?>