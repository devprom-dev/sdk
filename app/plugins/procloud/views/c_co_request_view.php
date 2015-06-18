<?php

/////////////////////////////////////////////////////////////////////////////////
class CoRequestPageContent extends CoPageContent
{
	function validate()
	{
		global $project_it, $_REQUEST, $model_factory;
		
		if ( !is_object($project_it) )
		{
			return false;
		}
		
		if ( !$project_it->IsPublic() )
		{
			return false;
		}

		if ( $_REQUEST['id'] != '' )
		{
			$request = $model_factory->getObject('pm_ChangeRequest');
			$this->request_it = $request->getExact($_REQUEST['id']);

			if ( $this->request_it->count() < 1 )
			{
				return false;
			}
		}
		
		return true;
	}
	
	function getTitle()
	{
		if ( is_object($this->request_it) )
		{
			return $this->request_it->getWordsOnly('Caption', 5).' - '.parent::getTitle();
		}
		else
		{
			return translate('Пожелания').' - '.parent::getTitle();
		}
	}
	
	function getKeywords()
	{
		return parent::getKeywords().' '.translate('пожелание').' '.translate('доработка').' '.
			translate('пожелания').' '.translate('ошибка').' '.translate('работает');
	}

	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		$request = $model_factory->getObject('pm_ChangeRequest');
		$comment = $model_factory->getObject('Comment');
	
		$this->drawProjectHeader( '<a href="/requests/'.
			$project_it->get('CodeName').'">'.translate('Пожелания').'</a>' );

		// introduction
		echo '<div id="bloglist">';
			if ( $_REQUEST['action'] == 'report' )
			{
				$this->drawReportForm();
			}
			else
			{
				if ( $_REQUEST['id'] != '' )
				{
					$this->drawRequest( $_REQUEST['id'] );
				}
				else
				{
					$this->drawRequests();
				}
			}
		echo '</div>';

		echo '<div id="user_actions">';

			echo '<div class="action_box">';
	
				$urlissue = '/requests/'.$project_it->get('CodeName').'/action/report';
				$urlbug = '/requests/'.$project_it->get('CodeName').'/action/report?bug';

				if ( !$user_it->IsReal() )
				{
					$urlissue = "javascript: getLoginForm('".$urlissue."')";
					$urlbug = "javascript: getLoginForm('".$urlbug."')";
				}

				$page->drawActionButton( 
					'<a href="'.$urlissue.'">'.text(747).'</a>' );
				
				echo '<div style="clear:both;"></div>';
				echo '<br/>';
				
				$page->drawActionButton( 
					'<a href="'.$urlbug.'">'.translate('Сообщить об ошибке').'</a>' );

			echo '</div>';
			
			if ( $user_it->IsReal() )
			{
				echo '<div class="action_box">';
					$page->drawGreyBoxBegin();
		
					echo '<div id="title">';
						echo translate('Мои пожелания');
					echo '</div>';
	
					$request = $model_factory->getObject('pm_ChangeRequest');
					$request->addFilter( new RequestAuthorFilter('my') );
					
					$request_it = $request->getLatest( 10 );
					while ( !$request_it->end() )
					{
						echo '<div class="request">';
							echo '<div id="comcount" title="'.translate('Комментарии').'" style="float:left;">';
								echo '<a href="'.ParserPageUrl::parse($request_it).'">'.$comment->getCount($request_it).'</a>';
							echo '</div>';

							echo '<div class="body" style="float:left;">';
								echo '<a href="'.ParserPageUrl::parse($request_it).'">'.
									$request_it->getWordsOnly('Caption', 5).'</a>';
							echo '</div>';

							echo '<div style="clear:both;">';
								echo translate('Статус').': '.$request_it->getStateName();
							echo '</div>';
						echo '</div>';
	
						echo '<div style="clear:both;"></div>';
						echo '<br/>';
	
						$request_it->moveNext();
					}
	
					$page->drawGreyBoxEnd();
				echo '</div>';
			}

		echo '</div>';

		
		echo '<div style="clear:both;">&nbsp;</div>';
		
		if ( $_REQUEST['action'] != '' && !$user_it->IsReal() )
		{
			echo '<script type="text/javascript">$(document).ready(function(){getLoginForm("'.$urlissue.'");});</script>';		
		}
	}
	
	function drawRequests()
	{
		global $model_factory, $project_it, $user_it;
		
		$page = $this->getPage();

		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->addFilter( new StatePredicate('submitted') );
		$request->addFilter( new RequestAuthorFilter('external') );
		
		$request_it = $request->getLatest( 10 );
								
		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			
			for ( $i = 0; $i < $request_it->count(); $i++ )
			{
				$this->drawRequestShort($request_it);
				$request_it->moveNext();
			}		
	
			$page->drawWhiteBoxEnd();
		echo '</div>';	
	}
	
	function drawRequestShort( $request_it )
	{
		global $model_factory;
		
		$page = $this->getPage();
		$comment = $model_factory->getObject('Comment');
		$author_it = $request_it->getRef('Author');

		echo '<div class="request">';

			echo '<div class="body" style="width:100%;">';
				echo '<div id="comcount" title="'.translate('Комментарии').'">';
					echo '<a href="'.ParserPageUrl::parse($request_it).'">'.$comment->getCount($request_it).'</a>';
				echo '</div>';

				echo '<h3 style="margin-top:0;"><a class="author" href="'.ParserPageUrl::parse($author_it).'">'.$author_it->getDisplayName().
					'</a>&nbsp;>&nbsp;<a href="'.ParserPageUrl::parse($request_it).'">'.$request_it->getWordsOnly('Caption', 5).'</a></h3>';
			echo '</div>';
				
			echo '<div style="clear:both;"></div>';

			echo '<div class="vote">';
				echo $request_it->getHtml('Description');
			echo '</div>';

			echo '<div style="clear:both;"></div>';
			echo '<br/>';

		echo '</div>';
		
		echo '<div style="clear:both;padding-bottom:20px;"></div>';
	}
	
	function drawRequest( $id )
	{
		global $model_factory, $project_it, $user_it;
		
		$page = $this->getPage();
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		$comment = $model_factory->getObject('Comment');
		$rating = $model_factory->getObject('co_Rating');
		
		$request_it = $request->getExact($id);
		$author_it = $request_it->getRef('Author');

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			
			if ( $request_it->IsFinished() )
			{
				$page->drawCompleteButton(
					$request_it->getStateName() );
			}
			else
			{
				$page->drawInProgressButton(
					$request_it->getStateName() );
			}

			if ( $project_it->HasUserAccess($user_it) )
			{
				$caption = '<a href="/pm/'.$project_it->get('CodeName').
					'/I-'.$request_it->getId().'">'.$request_it->getWordsOnly('Caption', 5).'</a>';
			}
			else
			{
				$caption = $request_it->getWordsOnly('Caption', 5);
			}
			
			echo '<div style="float:left;padding-left:10px;">';
				echo '<h3 style="margin-top:0;">'.$caption.'</h3>';
			echo '</div>';

			echo '<div style="clear:both;">';
			echo '</div>';

			echo '<br/>';

			echo '<div style="clear:both;">';
				echo $request_it->getDateFormat('RecordCreated').' '.
					'<a href="'.ParserPageUrl::parse($author_it).'">'.$author_it->getDisplayName().'</a>';
			echo '</div>';

			echo '<br/>';
			echo $request_it->getHtml('Description');

			echo '<div style="clear:both;">';
			echo '</div>';

			echo '<br/>';
			
			$this->drawComments( $request_it );

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}

	function drawReportForm()
	{
		global $model_factory;
		
		$page = $this->getPage();
		$form = new CoIssueReportForm( $model_factory->getObject('pm_ChangeRequest'));

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			$form->draw();
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

////////////////////////////////////////////////////////////////////////////////
class CoIssueReportForm extends CoPageForm
 {
 	var $question_it;
 	
 	function CoIssueReportForm ( $object )
 	{
 		global $model_factory;
 		
		$question = $model_factory->getObject('cms_CheckQuestion');
		$this->question_it = $question->getRandom();
		
		parent::AjaxForm( $object );
 	}
 	
 	function getAddCaption()
 	{
 		return translate('Добавление нового пожелания');
 	}

 	function getCommandClass()
 	{
 		return 'coreportissue';
 	}
 	
	function getAttributes()
	{
		$attrs = array ( 'Caption', 'Type', 'Question' );
		
    	return $attrs;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Caption':
				return 'richtext'; 	

			case 'Type':
			case 'Question':
				return 'text'; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return true;
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Caption':
				return translate('');

			case 'Type':
				return translate('Тип пожелания');

			case 'Question':
				return translate('Защита от спама').': "'.$this->question_it->getDisplayName().'"'; 	

			default:
				return parent::getName( $attribute );
				
		}
	}

 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
			case 'Caption':
				return text('procloud504');
 		
			case 'Type':
				return text('procloud505');

 			case 'Question':
 				return text('procloud456');
 		}
 	}
 	
 	function getButtonText()
 	{
 		return translate('Добавить');
 	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		global $tab_index, $_REQUEST;
		
		if ( $attribute == 'Question' )
		{
			?>
			<input class=input_value id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>">
			<input type="hidden" id="<? echo $attribute.'Hash'; ?>" name="<? echo $attribute.'Hash'; ?>" value="<? echo $this->question_it->getHash(); ?>">
			<?	
			
			$tab_index++;						
		}

		if ( $attribute == 'Type' )
		{
			$isbug = array_key_exists('bug', $_REQUEST);
			
			?>
			<select class=input_value name="Type" tabindex="<? echo $tab_index ?>">
				<option value="1" <? echo ($isbug ? "" : "selected") ?> ><? echo_lang('Доработка') ?></option>
				<option value="2" <? echo ($isbug ? "selected" : "") ?> ><? echo_lang('Ошибка') ?></option>
			</select>
			<?	
			
			$tab_index++;						
		}
	}
}

?>
