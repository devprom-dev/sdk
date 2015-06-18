<?php

/////////////////////////////////////////////////////////////////////////////////
class CoQuestionPageContent extends CoPageContent
{
	function validate()
	{
		global $project_it, $_REQUEST, $model_factory;
		
		if ( !is_object($project_it) )
		{
			return false;
		}
		
		return true;
	}
	
	function getTitle()
	{
		return translate('Вопросы').' - '.parent::getTitle();
	}
	
	function getKeywords()
	{
		return parent::getKeywords().' '.translate('вопрос').' '.translate('как').' '.
			translate('что').' '.translate('почему').' '.translate('где');
	}

	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$this->drawProjectHeader( '<a href="/questions/'.
			$project_it->get('CodeName').'">'.translate('Вопросы').'</a>' );

		// introduction
		echo '<div id="bloglist">';

			if ( $_REQUEST['action'] == 'ask' )
			{
				$this->drawAskQuestion();
			}
			else
			{
				$this->drawQuestions();
			}
			
		echo '</div>';

		echo '<div id="user_actions">';
			$ask_url = '/questions/'.$project_it->get('CodeName').'/action/ask';
		
			if ( $_REQUEST['action'] != 'ask' )
			{
				echo '<div class="action_box">';
		
					if ( !$user_it->IsReal() )
					{
						$ask_url = 'javascript: getLoginForm(\''.$ask_url.'\')';
					}
					
					$page->drawActionButton( 
						'<a href="'.$ask_url.'">'.translate('Задать вопрос').'</a>' );
	
				echo '</div>';
			}

			$question = $model_factory->getObject('pm_Question');
			
			if ( $user_it->IsReal() )
			{
				echo '<div class="action_box">';
					$page->drawGreyBoxBegin();
		
					echo '<div id="title">';
						echo translate('Мои вопросы');
					echo '</div>';
	
					$question->addFilter( new QuestionAuthorFilter($user_it->getId()) );
					$question_it = $question->getAll();
					
					while ( !$question_it->end() )
					{
						echo '<div class="body">';
							echo '<a href="'.ParserPageUrl::parse($question_it).'">'.
								$question_it->getWordsOnly('Caption', 8).'</a>';
						echo '</div>';
						
						echo '<br/>';
						$question_it->moveNext();
					}
	
					$page->drawGreyBoxEnd();
				echo '</div>';
			}

			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();

				echo '<div id="title">';
					echo translate('Обсуждения');
				echo '</div>';

				$comment = $model_factory->getObject('Comment');
				$comment_it = $comment->getByEntities( array('question') );

				while ( !$comment_it->end() )
				{
					$object_it = $comment_it->getAnchorIt();
					if ( $object_it->count() < 1 )
					{
						$comment_it->moveNext();
						continue;
					}

					echo '<div id="comcount" title="'.translate('Комментарии').'">';
						echo '<a href="'.ParserPageUrl::parse($object_it).'#comment">'.$comment_it->get('CommentsCount').'</a>';
					echo '</div>';

					echo '<div style="float:left;">'.$comment_it->getDateTimeFormat('RecordModified').'</div>';
						
					echo '<div style="padding-bottom:16px;clear:both;">'.
						$object_it->getWordsOnly('Caption', 12).'</div>';

					$comment_it->moveNext();
				}

				$page->drawGreyBoxEnd();
			echo '</div>';			

		echo '</div>';

		echo '<div style="clear:both;">&nbsp;</div>';
		
		if ( $_REQUEST['action'] != '' && !$user_it->IsReal() )
		{
			echo '<script type="text/javascript">$(document).ready(function(){getLoginForm("'.$ask_url.'");});</script>';		
		}
	}

	function drawQuestions()
	{	
		global $model_factory, $project_it, $user_it;

		$page = $this->getPage();
		
		$comment = $model_factory->getObject('Comment');
		
		$question = $model_factory->getObject('pm_Question');
		$question->defaultsort = ' RecordCreated DESC ';
		
		$question_it = $question->getAll();

		while ( !$question_it->end() )
		{
			$author_it = $question_it->getRef('Author');
			
			echo '<div class="post">';
				$page->drawWhiteBoxBegin();
				
				echo '<a name="'.$question_it->getSearchName().'" title="'.$question_it->getId().'"></a>';

				$caption = $question_it->getWordsOnly('Caption', 5);

				$caption = '<a href="/questions/'.$project_it->get('CodeName').'#'.$question_it->getSearchName().'">'.$caption.'</a>';
		
				echo '<div style="float:left;">';
					echo '<h3><a class="author" href="'.ParserPageUrl::parse($author_it).'">'.$author_it->getDisplayName().
						'</a> > '.$caption.'</h3>';
				echo '</div>';
				
				echo '<div style="clear:both;">';
				echo '</div>';

				echo '<br/>';						

				echo '<div>';
					echo $question_it->getHtml('Content');
				echo '</div>';

				echo '<br/>';						

				echo '<div class="commentsholder" id="comments'.$question_it->getId().'">';
					echo '<div id="comcount" title="'.translate('Комментарии').'">';
						echo '<a href="javascript: initComments(\''.$project_it->get('CodeName').'\', ' .
							'\'question\', \''.$question_it->getId().'\')">'.
								$comment->getCount($question_it).'</a>';
					echo '</div>';
				echo '</div>';
				
				$page->drawWhiteBoxEnd();
			echo '</div>';
			
			echo '<br/>';						
			$question_it->moveNext();
		}
	}
	
	function drawScripts()
	{
		global $project_it;
		
		?>
		<script language="javascript">
 			$(document).ready(function() { 
 				var locstr = new String(decodeURI(window.location));
				if ( locstr.indexOf('#comment') > 0 )
				{
					var commentString = locstr.substring(locstr.indexOf('#'));
					var parts = commentString.split('#');
					
					if ( parts.length > 0 )
					{
						initComments('<? echo $project_it->get('CodeName')?>', 'question', 
							$('a[name="'+parts[1]+'"]').attr('title') );
					}
				}
 			});
		</script>
		<?		
	}

	function drawAskQuestion()
	{
		global $model_factory;
		
		$page = $this->getPage();
		$form = new CoAskQuestionForm( $model_factory->getObject('pm_Question'));

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			$form->draw();
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

 ////////////////////////////////////////////////////////////////////////////////
 class CoAskQuestionForm extends CoPageForm
 {
 	var $question_it;
 	
 	function CoAskQuestionForm ( $object )
 	{
 		global $model_factory;
 		
		$question = $model_factory->getObject('cms_CheckQuestion');
		$this->question_it = $question->getRandom();
		
		parent::AjaxForm( $object );
 	}
 	
 	function getAddCaption()
 	{
 		return translate('Вопрос участникам проекта');
 	}

 	function getCommandClass()
 	{
 		return 'coaskquestion';
 	}
 	
	function getAttributes()
	{
		$attrs = array ( 'Caption', 'Question' );
		
    	return $attrs;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Caption':
				return 'richtext'; 	

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
				return text('procloud503');
 		
 			case 'Question':
 				return text('procloud456');
 		}
 	}
 	
 	function getButtonText()
 	{
 		return translate('Отправить');
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
	}
 }
 
?>
