<?php

/////////////////////////////////////////////////////////////////////////////////
class CoPollsPageContent extends CoPageContent
{
	function validate()
	{
		global $project_it;
		
		if ( is_object($project_it) && !$project_it->IsPublic() )
		{
			return false;
		}
		
		return true;
	}
	
	function getTitle()
	{
		return translate('Опросы').' - '.parent::getTitle();
	}
	
	function getKeywords()
	{
		return parent::getKeywords().' '.translate('опрос').' '.
			translate('мнение').' '.translate('отзыв');
	}

	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$poll = $model_factory->getObject('pm_Poll');
		$project = $model_factory->getObject('pm_Project');

		$allprojectsmode = !is_object($project_it);
		$page = $this->getPage();
		
		if ( !$allprojectsmode )
		{
			$this->drawProjectHeader( '<a href="/polls/'.
				$project_it->get('CodeName').'">'.translate('Опросы').'</a>' );
		}
		else
		{
			$this->drawHeader( '<a href="/polls">'.
				translate('Опросы').'</a>' );
		}

		// introduction
		echo '<div id="bloglist">';
			$this->drawPolls();
		echo '</div>';

		echo '<div id="user_actions">';
			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();
	
				echo '<div id="title">';
					echo translate('Новые опросы');
				echo '</div>';
	
				echo '<div>';
					$poll_it = $poll->getAllPublicIt();
					
			 		while( !$poll_it->end() )
			 		{
						$project_it = $project->getExact($poll_it->get('Project'));
						$session = new PMSession($project_it->getId());

			 			echo '<div>';
			 				echo '<a href="'.ParserPageUrl::parse($poll_it).'">'.$poll_it->getDisplayName().'</a>';
						echo '</div>';
						
						echo '<br/>';

			 			$poll_it->moveNext();
			 		}
				echo '</div>';
	
				if ( !$allprojectsmode )
				{
					$page->drawBlackButton( 
						'<a href="/polls">'.translate('Все опросы').'</a>' );
		
				}
			
				$page->drawGreyBoxEnd();
			echo '</div>';
		echo '</div>';

		echo '<div style="clear:both;">&nbsp;</div>';
	}

	function drawPolls()
	{	
		global $model_factory, $project_it, $user_it;

		$page = $this->getPage();
		
		$project = $model_factory->getObject('pm_Project');
		$poll = $model_factory->getObject('pm_Poll');
		$allprojectsmode = !is_object($project_it);
		
		if ( !$allprojectsmode )
		{
			$poll_it = $poll->getPublicIt();
		}
		else
		{
			$poll_it = $poll->getAllPublicIt( 20 );
		}

		while ( !$poll_it->end() )
		{
			if ( $poll_it->get('Project') > 0 )
			{
				$project_it = $project->getExact($poll_it->get('Project'));
				$session = new PMSession($project_it->getId());
			}
			
			echo '<div class="post">';
				$page->drawWhiteBoxBegin();
				$voices = '('.translate('ответов').': '.$poll_it->getRespondents().')';
				
				echo '<div style="float:left;">';
					if ( !$allprojectsmode )
					{
						echo '<h3><a class="author" href="'.ParserPageUrl::parse($poll_it).'">'.
							$poll_it->getDisplayName().'</a> '.$voices.'</h3>';
					}
					else
					{
						echo '<h3><a class="author" href="'.ParserPageUrl::parse($project_it).'">'.
							$project_it->getWordsOnly('Caption', 5).'</a> > <a href="'.ParserPageUrl::parse($poll_it).'">'.
								$poll_it->getDisplayName().'</a> '.$voices.'</h3>';
					}
				echo '</div>';
				
				echo '<div style="clear:both;">';
				echo '</div>';
				echo '<br/>';						

				echo '<div>';
					echo $poll_it->getHtml('Description');
				echo '</div>';

				$page->drawWhiteBoxEnd();
			echo '</div>';
			
			echo '<br/>';						
			$poll_it->moveNext();
		}	
	}
}
 
/////////////////////////////////////////////////////////////////////////////////
class CoPollPageContent extends CoPollsPageContent
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
		
		if ( $_REQUEST['poll'] == '' )
		{
			return false;
		}
		else
		{
			$poll = $model_factory->getObject('pm_Poll');
			$this->poll_it = $poll->getExact($_REQUEST['poll']);
			
			if ( $this->poll_it->count() < 1 )
			{
				return false;
			}
			
			$this->participated = $this->poll_it->IsUserParticipated();
		}

		return true;
	}
	
	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		
		$this->drawProjectHeader( '<a href="/polls/'.
			$project_it->get('CodeName').'">'.translate('Опросы').'</a>' );

		// introduction
		echo '<div id="bloglist">';
			if ( in_array('results', array_keys($_REQUEST)) )
			{
				$this->drawPoll();
			}
			else
			{
				if ( $this->participated )
				{	
					$this->drawPoll();
				}
				else
				{
					$this->askOnPoll();
				}
			}
		echo '</div>';

		echo '<div id="user_actions">';
			echo '<div class="action_box">';
				echo '<div class="addthis_toolbox addthis_default_style">';
				echo '<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=devprom" class="addthis_button_compact">Поделиться</a>';
				echo '<span class="addthis_separator">|</span>';
				echo '<a class="addthis_button_facebook"></a>';
				echo '<a class="addthis_button_myspace"></a>';
				echo '<a class="addthis_button_google"></a>';
				echo '<a class="addthis_button_twitter"></a>';
				echo '</div>';
				echo '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=devprom"></script>';
			echo '</div>';

			if ( !$this->participated )
			{
				$page->drawActionButton( 
					'<a href="?results">'.translate('Результаты').'</a>' );
				
				echo '<br/>';
			
				if ( in_array('results', array_keys($_REQUEST)) )
				{
					$page->drawActionButton( 
						'<a href="'.ParserPageUrl::parse($this->poll_it).'">'.translate('Участвовать').'</a>' );

					echo '<br/>';
				}
			}

			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();
	
				echo '<div id="title">';
					echo translate('Участники');
				echo '</div>';
	
				echo '<div>';
			 		$part_it = $this->poll_it->getUserIt();
			 		while( !$part_it->end() )
			 		{
			 			$this->drawUser( $part_it );
			 			$part_it->moveNext();
			 		}
				echo '</div>';
	
				echo '<br/>';
				
				echo '<div>';
					echo translate('Всего участников').': '.$this->poll_it->getRespondents();
				echo '</div>';

				$page->drawGreyBoxEnd();
			echo '</div>';

			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();
	
				echo '<div id="title">';
					echo translate('Другие опросы');
				echo '</div>';
	
				echo '<div>';
					$poll_it = $this->poll_it->object->getPublicIt();
					
			 		while( !$poll_it->end() )
			 		{
			 			if ( $poll_it->getId() != $this->poll_it->getId() )
			 			{
				 			echo '<div>';
				 				echo '<a href="'.ParserPageUrl::parse($poll_it).'">'.$poll_it->getDisplayName().'</a>';
							echo '</div>';
							
							echo '<br/>';
			 			}
			 			$poll_it->moveNext();
			 		}
				echo '</div>';
	
				$page->drawGreyBoxEnd();
			echo '</div>';
		echo '</div>';

		echo '<div style="clear:both;">&nbsp;</div>';
	}

	function drawPoll()
	{	
		global $model_factory, $project_it, $user_it;

		$page = $this->getPage();
		
		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			
			if ( $project_it->HasUserAccess($user_it) )
			{
				echo '<div style="float:left;margin-right:8px;">';
					$page->drawBlackButton('<a href="/pm/'.$project_it->get('CodeName').
						'/'.$this->poll_it->getItemsUrl().'">'.translate('Редактировать').'</a>');	
				echo '</div>';
			}

			echo '<div style="float:left;">';
				echo '<h3>'.$this->poll_it->getDisplayName().'</h3>';
			echo '</div>';
			
			echo '<div style="clear:both;"></div>';
			echo '<br/>';						

			echo '<div>';
				echo $this->poll_it->getHtml('Description');
			echo '</div>';

			$page->drawWhiteBoxEnd();
			
			echo '<div style="clear:both;"></div>';
			echo '<br/>';						

			$item_it = $this->poll_it->getItems();
			while ( !$item_it->end() )
			{
	 			if ( $item_it->isSection() )
	 			{
					echo '<div>';
						echo '<h2>'.$item_it->getDisplayName().'</h2>';
					echo '</div>';
	 			}
	 			else
	 			{
 					$answer_it = $this->poll_it->getAnswerIt($item_it);
 					
					$page->drawWhiteBoxBegin();
					
					echo '<div style="padding-bottom:12px;">';
						echo '<h3>'.$item_it->getDisplayName().'</h3>';
					echo '</div>';
					
					echo '<div>';
		 				$answers = preg_split('/'.Chr(10).'/', $item_it->get('Answers'));
		 				
		 				$results = array();
		 				for( $j = 0; $j < count($answers); $j++ )
		 				{
		 					array_push($results, 0);
		 				}
		 				
		 				for ( $j = 0; $j < $answer_it->count(); $j++ )
		 				{
		 					$results[$answer_it->get('Answer')]++;
		 					$answer_it->moveNext();
		 				}
		 				
		 				for( $j = 0; $j < count($answers); $j++ )
		 				{
		 					if ( $answers[$j] == '' )
		 					{
		 						continue;
		 					}
		 					
		 					$this->drawProgress( round($results[$j] / $answer_it->count() * 100) );
		 					
		 					echo '<div style="float:left;margin-top:-6px;padding-bottom:12px;"> ('.
		 						$results[$j].') &nbsp; '.$answers[$j].'</div>';
		 						
							echo '<div style="clear:both;"></div>';
		 				}
					echo '</div>';
					
					$page->drawWhiteBoxEnd();
	
	 			}
				
				echo '<div style="clear:both;"></div>';
				echo '<br/>';		

				$item_it->moveNext();				
			}
			
		echo '</div>';
	}

	function askOnPoll()
	{	
		$form = new CoPollForm( $this->poll_it, $this->getPage() );
		$form->draw();
	}

	function drawProgress( $percent )
	{
		echo '<div id="progress" style="float:left;width:210px;">';
			echo '<div id="plt">&nbsp;</div>';
			echo '<div id="full" style="width:'.round(180 * $percent / 100).'px;">&nbsp;</div>';
			echo '<div id="empty" style="width:'.round(180 * (100 - $percent ) / 100).'px;">&nbsp;</div>';
			echo '<div id="prt">&nbsp;</div>';
		echo '</div>';
	}

	function drawUser( $user_it, $width = 24 )
	{
 		echo '<a href="'.ParserPageUrl::parse($user_it).'" title="'.$user_it->getDisplayName().'">'.
 			'<img class="photo" style="width:'.$width.'px;height:'.$width.'px;padding:2px;" src="'.
 				ParserPageUrl::getPhotoUrl($user_it, $width).'" border=0></a> ';
	}

	function getTitle()
	{
		return translate('Опрос').': '.$this->poll_it->getDisplayName();
	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class CoPollForm extends CoPageForm
 {
 	var $poll_it, $page;
 	
 	function CoPollForm ( $poll_it, $page )
 	{
 		$this->poll_it = $poll_it;	
 		$this->page = $page;
 			
		parent::CoPageForm( $poll_it );
 	}

 	function getCommandClass()
 	{
 		return 'copoll';
 	}
 	
	function getAttributes()
	{
    	return array();
	}
	
	function getButtonText()
	{
		return translate('Отправить');
	}
	
	function draw()
	{
		global $model_factory, $project_it, $user_it;

		$form_processor_url = '/command/'.$this->getCommandClass();
	
		$this->drawScript();
		
		echo '<div>';
			echo '<form id="myForm" action="'.$form_processor_url.'" method="post" style="width:100%;">';
				echo '<input type="hidden" id="action" name="action" value="'.$this->getAction().'">';
				echo '<input type="hidden" id="object_id" name="object_id" value="'.$this->poll_it->getId().'">';
				echo '<input type="hidden" id="project" name="project" value="'.$project_it->get('CodeName').'">';

				echo '<div class="post">';
					$this->page->drawWhiteBoxBegin();
					
					echo '<div style="float:left;">';
						echo '<h3>'.$this->poll_it->getDisplayName().'</h3>';
					echo '</div>';
					
					echo '<div style="clear:both;"></div>';
					echo '<br/>';						
			
					echo '<div>';
						echo $this->poll_it->getHtml('Description');
					echo '</div>';
					echo '<br/>';						
			
					$this->page->drawWhiteBoxEnd();
					
					echo '<div style="clear:both;"></div>';
					echo '<br/>';						
			
					$item_it = $this->poll_it->getItems();
					while ( !$item_it->end() )
					{
			 			if ( $item_it->isSection() )
			 			{
							echo '<div>';
								echo '<h2>'.$item_it->getDisplayName().'</h2>';
							echo '</div>';
			 			}
			 			else
			 			{
							$answer_it = $this->poll_it->getAnswerIt($item_it);
							
							$this->page->drawWhiteBoxBegin();
							
							echo '<div style="padding-bottom:12px;">';
								echo '<h3>'.$item_it->getDisplayName().'</h3>';
							echo '</div>';
							
							echo '<div>';
				 				$answers = preg_split('/'.Chr(10).'/', $item_it->get('Answers'));
				 				
				 				for( $j = 0; $j < count($answers); $j++ )
				 				{
				 					if ( $answers[$j] == '' )
				 					{
				 						continue;
				 					}
				 					
				 					echo '<div style="float:left;padding-bottom:12px;">'.
				 						'<input type="radio" name="item'.$item_it->getId().'" value="'.$j.
										'" style="width:21px;margin-top:6px;"><label for="item'.$item_it->getId().'">'.$answers[$j].'</label></div>';
				 						
									echo '<div style="clear:both;"></div>';
				 				}
							echo '</div>';
							
							$this->page->drawWhiteBoxEnd();
			
			 			}
						
						echo '<div style="clear:both;"></div>';
						echo '<br/>';		
			
						$item_it->moveNext();				
					}
					
				echo '</div>';
			echo '</form>';
			
			echo '<div id="result" style="clear:both;padding-bottom:12px;"></div>';

			echo '<div id="buttons" style="width:100%;">';
				if ( !$user_it->IsReal() )
				{
					echo '<div class="blackbutton" id="submitbutton" style="padding-right:12px;">';
						echo '<div id="body" style="width:120px;text-align:center;">';
							echo '<a id="submit" href="javascript: getLoginForm();">'.translate('Авторизоваться').'</a>';
						echo '</div>';
						echo '<div id="rt"></div>';
					echo '</div>';
				}
			
				echo '<div class="blackbutton" id="submitbutton" style="padding-right:12px;">';
					echo '<div id="body" style="width:80px;text-align:center;">';
						echo '<a id="submit" href="javascript: '.$this->getSubmitScript().'">'.$this->getButtonText().'</a>';
					echo '</div>';
					echo '<div id="rt"></div>';
				echo '</div>';
			echo '</div>';

			echo '<div style="clear:both;"></div>';
			echo '<br/>';

		echo '</div>';
	}

	function getSubmitScript()
	{
/*		return 'submitForm(\''.$this->getAction().
			'\', function(){window.location = "'.ParserPageUrl::parse($this->poll_it).'";})';
*/
		return 'submitForm(\''.$this->getAction().'\', refreshWindow)';
	}
 }
 
?>
