<?php

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class MessageIterator extends OrderedIterator
 {
	function getViewUrl()
	{
		return CoController::getMessageUrl($this->getId());
	}
	
	function getCaption()
	{
		global $user_it;
		
		if ( $this->get('Author') == $user_it->getId() )
		{
			if ( $this->get('ToUser') > 0 )
			{
				$user_it = $this->getRef('ToUser');
				return $this->get('Subject').' ('.translate('для').' '.$user_it->getDisplayName().')';
			}
			else
			{
				$team_it = $this->getRef('ToTeam');
				return $this->get('Subject').' ('.translate('для').' '.$team_it->getDisplayName().')';
			}
		}
		else
		{
			$user_it = $this->getRef('Author');
			return $this->get('Subject').' ('.translate('от').' '.$user_it->getDisplayName().')';
		}
	}
	
	function get_native( $attr )
	{
		if ( $attr == 'Caption' )
		{
			return parent::get_native( 'Subject' );
		}
		
		return parent::get_native( $attr ); 
	}
	
	function getAddressee()
	{
		if ( $this->get('ToUser') > 0 )
		{
			$user_it = $this->getRef('ToUser');
			return $user_it->getRefLink();
		}
		else
		{
			$team_it = $this->getRef('ToTeam');
			return $team_it->getRefLink();
		}
	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class Message extends Metaobject
 {
 	function Message() 
 	{
 		parent::Metaobject('co_Message');

		$this->defaultsort = 'RecordModified DESC';
		$this->addAttribute('Caption', 'TEXT', translate('Название'), false);
 	}
 	
 	function createIterator() 
 	{
 		return new MessageIterator( $this );
 	}
 	
	function getPage() {
		return 'profile.php?';
	}
	
 	function getAttributeDbName( $name ) 
 	{
		if ( $name == 'Caption' )
		{
			return parent::getAttributeDbName( 'Subject' );
		}
		
		return parent::getAttributeDbName( $name ); 
	}

	function getDefaultAttributeValue( $name )
	{
		global $user_it;
		
		switch ( $name )
		{
			case 'Author':
				return $user_it->getId();
				
			default:
				return parent::getDefaultAttributeValue($name);
		}
	}
	
	function getMyIt( $limit = 0, $page = 0)
	{
		global $user_it;
		
		$sql = " SELECT m.*, (CASE IFNULL((SELECT c.AuthorId FROM Comment c " .
			   "			   WHERE c.ObjectId = m.co_MessageId" .
			   "				 AND c.ObjectClass = 'message' ORDER BY c.RecordCreated DESC LIMIT 1 ), -1) " .
			   "			  WHEN m.Author THEN 0 WHEN -1 THEN 0 ELSE 1 END) NeedReply " .
			   "   FROM co_Message m " .
			   "  WHERE m.Author = ".$user_it->getId().
			   "  UNION " .
			   " SELECT m.*, (CASE IFNULL((SELECT c.AuthorId FROM Comment c " .
			   "			   WHERE c.ObjectId = m.co_MessageId" .
			   "				 AND c.ObjectClass = 'message' ORDER BY c.RecordCreated DESC LIMIT 1 ), -1) " .
			   "			  WHEN m.ToUser THEN 0 WHEN -1 THEN 1 ELSE 1 END) NeedReply " .
			   "   FROM co_Message m " .
			   "  WHERE m.ToUser = ".$user_it->getId().
			   "  ORDER BY RecordCreated DESC" .
			   ($limit > 0 ? "  LIMIT ".$limit." OFFSET ".($page * $limit) : "");
			   
		return $this->createSQLIterator($sql);
	}

	function getUnread()
	{
		global $user_it;
		
		$sql = " SELECT IFNULL(SUM(NeedReply), 0) cnt FROM ( ".
				   " SELECT (CASE IFNULL((SELECT c.AuthorId FROM Comment c " .
				   "			   WHERE c.ObjectId = m.co_MessageId" .
				   "				 AND c.ObjectClass = 'message' ORDER BY c.RecordCreated DESC LIMIT 1 ), -1) " .
				   "			  WHEN m.Author THEN 0 WHEN -1 THEN 0 ELSE 1 END) NeedReply " .
				   "   FROM co_Message m " .
				   "  WHERE m.Author = ".$user_it->getId().
				   "  UNION ALL " .
				   " SELECT (CASE IFNULL((SELECT c.AuthorId FROM Comment c " .
				   "			   WHERE c.ObjectId = m.co_MessageId" .
				   "				 AND c.ObjectClass = 'message' ORDER BY c.RecordCreated DESC LIMIT 1 ), -1) " .
				   "			  WHEN m.ToUser THEN 0 WHEN -1 THEN 1 ELSE 1 END) NeedReply " .
				   "   FROM co_Message m " .
				   "  WHERE m.ToUser = ".$user_it->getId().
			   "  ) t ";
			   
		$it = $this->createSQLIterator($sql);
		
		return $it->get('cnt');
	}
	
	function getTeamIt( $team_id )
	{
		$sql = " SELECT m.* " .
			   "   FROM co_Message m " .
			   "  WHERE m.ToTeam = " .$team_id.
			   "  ORDER BY m.RecordCreated DESC" .
			   "  LIMIT 10 ";
			   
		return $this->createSQLIterator($sql);
	}
 }

 ?>
