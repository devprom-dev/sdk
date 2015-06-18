<?php

/////////////////////////////////////////////////////////////////////////////////
class CoMessagePageContent extends CoPageContent
{
	function validate()
	{
		global $_REQUEST, $model_factory, $user_it;
		
		if ( !$user_it->IsReal() )
		{
			return false;
		}
		
		if ( $_REQUEST['id'] != '' && $_REQUEST['action'] != 'send' )
		{
			$message = $model_factory->getObject('co_Message');
			$message_it = $message->getExact($_REQUEST['id']);
			
			if ( $message_it->count() < 1 )
			{
				return false;
			}
			
			$owners = array ( $message_it->get('ToUser'), $message_it->get('Author') );
			if ( !in_array( $user_it->getId(), $owners ) )
			{
				return false;
			}
			
			switch ( $_REQUEST['action'] )
			{
				case 'remove':
					$message_it->object->delete($message_it->getId());
					
					exit(header('Location: /messages'));
					break;
			}
		}
		
		return true;
	}
	
	function getTitle()
	{
		return translate('Сообщения').' - '.parent::getTitle();
	}
	
	function getKeywords()
	{
		return '';
	}

	function draw()
	{
		global $model_factory, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		
		// introduction
		echo '<div style="float:left;width:100%;">';

			echo '<div style="float:left;">';
				echo '<div id="grbutton" style="width:220px;">';
					echo '<div id="lt">&nbsp;</div>';
					echo '<div id="bd"><div style="padding-top:4px;"><a href="/messages">'.translate('Сообщения').'</a></div></div>';
					echo '<div id="rt">&nbsp;</div>';
					echo '<div id="an"></div>';
				echo '</div>';
			echo '</div>';

			echo '<div style="clear:both;"></div>';
			echo '<br/>';						
			
			if ( $_REQUEST['action'] == 'send' )
			{
				$this->drawSend();
			}
			else
			{
				if ( $_REQUEST['id'] != '' )
				{
					$this->drawMessage( $_REQUEST['id'] );
				}
				else
				{
					$this->drawMessages();
				}
			}
			
		echo '</div>';
	}

	function drawMessageTitle( $message_it )
	{
		global $user_it;
		
		echo '<div class="body">';
			echo '<h3>';
				if ( $message_it->get('Author') != $user_it->getId() )
				{
					$author_it = $message_it->getRef('Author');
					echo '<a class="author" href="'.ParserPageUrl::parse($author_it).'">'.$author_it->getDisplayName().'</a> > ';
				}
				
				echo '<a class="'.( $message_it->get('NeedReply') == 0 ? 'viewed' : '').'" href="'.
					ParserPageUrl::parse($message_it).'">'.
						$message_it->getWordsOnly('Subject', 15).'</a>';

				if ( $message_it->get('ToUser') != $user_it->getId() )
				{
					$author_it = $message_it->getRef('ToUser');
					echo ' > <a class="author" href="'.ParserPageUrl::parse($author_it).'">'.$author_it->getDisplayName().'</a>';
				}
			echo '</h3>';
		echo '</div>';
	}
	
	function drawMessages()
	{	
		global $model_factory, $user_it;

		$page = $this->getPage();
		
		$message = $model_factory->getObject('co_Message');

		$total_it = $message->getMyIt();
		$message_it = $message->getMyIt( 10, $_REQUEST['page'] );

		$comment = $model_factory->getObject2('Comment', $message_it);

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			while ( !$message_it->end() )
			{
				echo '<div id="comcount" title="'.translate('Комментарии').'">';
					echo '<a href="'.ParserPageUrl::parse($message_it).'">'.$comment->getCount($message_it).'</a>';
				echo '</div>';
	
				$this->drawMessageTitle( $message_it );
				
				echo '<br/>';						
				$message_it->moveNext();
			}	
	
			if ( $message_it->count() < 1 )
			{
				echo text('procloud517');
			}
			
			$this->drawPaging( $total_it->count(), 10 );

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
	
	function drawMessage( $message_id )
	{	
		global $model_factory, $user_it;

		$page = $this->getPage();
		
		$message = $model_factory->getObject('co_Message');
		$message_it = $message->getExact( $message_id );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			$this->drawMessageTitle( $message_it );
				
			echo '<div style="clear:both;">';
			echo '</div>';
			echo '<br/>';

			$page->drawBlackButton('<a href="/message/remove/'.$message_it->getId().'">'.translate('Удалить').'</a>');
				
			echo '<div style="clear:both;">';
			echo '</div>';
			echo '<br/>';

			echo $message_it->getHtml('Content');

			echo '<div style="clear:both;">';
			echo '</div>';

			echo '<br/>';
			
			$this->drawComments( $message_it );

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
	
	function drawSend()
	{
		global $model_factory;
		
		$page = $this->getPage();
		$form = new CoMessageForm( $model_factory->getObject('co_Message') );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			echo '<div style="width:60%;">';
				$form->draw();
			echo '</div>';
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

 ////////////////////////////////////////////////////////////////////////////////
 class CoMessageForm extends CoPageForm
 {
 	var $question_it;
 	
 	function CoMessageForm ( $object )
 	{
 		global $model_factory;
 		
		$question = $model_factory->getObject('cms_CheckQuestion');
		$this->question_it = $question->getRandom();
		
		parent::CoPageForm( $object );
 	}
 	
 	function getAddCaption()
 	{
 		return translate('Новое сообщение');
 	}

 	function getCommandClass()
 	{
 		return 'cosendmessage';
 	}
 	
	function getAttributes()
	{
		$attrs = parent::getAttributes();
		
		array_push( $attrs, 'Question' );
		
    	return $attrs;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Question':
				return 'text'; 	
				
			default:
				return parent::getAttributeType( $attribute );
		}
	}
 	
	function IsAttributeVisible( $attribute )
	{
		switch ( $attribute )
		{
			case 'Author':
			case 'ToTeam':
				return false;
				
			default:
				return true;
		}
	}
	
	function IsAttributeModifable( $attribute )
	{
		switch ( $attribute )
		{
			case 'ToUser':
			case 'Author':
				return false;
				
			default:
				return true;
		}
	}

	function getAttributeValue( $attribute )
	{
		global $_REQUEST, $model_factory;
		
		$value = parent::getAttributeValue( $attribute );
		
		if ( $value == '' )
		{
			switch( $attribute )
			{
				case 'ToUser':
					$user = $model_factory->getObject('cms_User');
					$user_it = $user->getExact($_REQUEST['id']);
					
					return $user_it->getId();
			}
		}
		else
		{
			return $value;
		}
	}

 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
 			case 'Subject':
 				return text('procloud178');

 			case 'Content':
 				return text('procloud179');

 			case 'Question':
 				return text('procloud456');
 		}
 	}

	function getButtonText()
	{
		return translate('Отправить');
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Question':
				return translate('Защита от спама').': "'.$this->question_it->getDisplayName().'"'; 	

			default:
				return parent::getName( $attribute );
		}
	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		global $tab_index;
		
		if ( $attribute == 'Question' )
		{
			?>
			<input class=input_value id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>">
			<input type="hidden" id="<? echo $attribute.'Hash'; ?>" name="<? echo $attribute.'Hash'; ?>" value="<? echo $this->question_it->getHash(); ?>">
			<?	
			
			$tab_index++;						
		}
		else
		{
			parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}
 }
 
?>
