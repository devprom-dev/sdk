<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_copublishproject.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 class CoPoll extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory, $user_it, $project_it;

		// proceeds with validation
		if ( !is_object($project_it) )
		{
			return false;
		}
		
		return true;
 	}
 	
 	function modify( $id )
	{
		global $_REQUEST, $model_factory, $project_it, $user_it;

		$poll = $model_factory->getObject('pm_Poll');
		$poll_it = $poll->getExact($id);
		
		if ( $poll_it->count() < 1 )
		{
			return;
		}
		
		$result = $model_factory->getObject('pm_PollResult');	

		if ( is_object($user_it) )
		{
			$result_id = $result->add_parms(
				array('Poll' => $poll_it->getId(), 'User' => $user_it->getId(),
					  'IsCurrent' => 'Y' ));
		}
		else
		{
			$user = $model_factory->getObject('cms_User');
			$user_it = $user->getAnonymousIt();
			 
			$result_id = $result->add_parms(
				array('Poll' => $poll_it->getId(), 'User' => $user_it->getId(),
					  'IsCurrent' => 'Y', 'AnonymousHash' => $poll_it->getAnonymousHash() ) );
		}

		$result_item = $model_factory->getObject('pm_PollItemResult');
		
		$item_it = $poll_it->getItems();
		for( $i = 0; $i < $item_it->count(); $i++ )
		{
			if ($_REQUEST['item'.$item_it->getId()] != '' )
			{
				$item_id = $result_item->add_parms(
					array('PollResult' => $result_id, 
						  'PollItem' => $item_it->getId(), 
						  'Answer' => $_REQUEST['item'.$item_it->getId()]) );
			}
			
			$item_it->moveNext();
		}

		$this->replySuccess( 
			$this->getResultDescription( 1000 ) );
	}

	function getResultDescription( $result )
	{
		global $project_it;
		
		switch($result)
		{
			case 1000:
				return text('procloud602');

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 
?>