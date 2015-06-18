<?php

///////////////////////////////////////////////////////////////////////////////////
class CoPage
{
	var $devprom_it, $content;
	
	function getContent()
	{
		if ( !is_object($this->content) )
		{
			$this->content = new CoPageContent;
		}

		return $this->content;
	}
	
	function getDevpromProjectIt()
	{
		global $model_factory;
		
		if ( !is_object($this->devprom_it) )
		{
			$project = $model_factory->getObject('pm_Project');
			$this->devprom_it = $project->getByRef('CodeName', 'procloud');
		}
			
		return $this->devprom_it;
	}
	
	function drawMenu()
	{
	}
	
	function drawWhiteBoxBegin()
	{
		echo '<div class="wb_header">';
			echo '<div id="tp">';
				echo '<div id="lt">';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div class="wb_body">';
			echo '<div id="md">';
	}
	
	function drawWhiteBoxEnd()
	{
			echo '</div>';
		echo '</div>';
		echo '<div class="wb_footer">';
			echo '<div id="bt">';
				echo '<div id="lt">';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}

	function drawGreyBoxBegin()
	{
		echo '<div class="gb_header">';
			echo '<div id="tp">';
				echo '<div id="lt">';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div class="gb_body">';
			echo '<div id="md">';
	}
	
	function drawGreyBoxEnd()
	{
			echo '</div>';
		echo '</div>';
		echo '<div class="gb_footer">';
			echo '<div id="bt">';
				echo '<div id="lt">';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}

	function drawBlackButton( $text )
	{
		echo '<div class="blackbutton">';
			echo '<div id="body">';
				echo '<div id="text">'.$text.'</div>';
			echo '</div>';
			echo '<div id="rt"></div>';
		echo '</div>';
	}
	
	function drawCompleteButton( $text )
	{
		echo '<div class="completebutton">';
			echo '<div id="lt">';
			echo '</div>';
			echo '<div id="body">';
				echo $text;
			echo '</div>';
			echo '<div id="rt">';
			echo '</div>';
		echo '</div>';
	}

	function drawInProgressButton( $text )
	{
		echo '<div class="inprogressbutton">';
			echo '<div id="lt">';
			echo '</div>';
			echo '<div id="body">';
				echo $text;
			echo '</div>';
			echo '<div id="rt">';
			echo '</div>';
		echo '</div>';
	}

	function drawActionButton( $text )
	{
		echo '<div class="actionbutton">';
			echo '<div id="body">';
				echo $text;
			echo '</div>';
		echo '</div>';
	}
	
	function drawShareActionBox()
	{
		echo '<div class="action_box">';
			$this->drawGreyBoxBegin();
			echo '<div class="addthis_toolbox addthis_default_style">';
			echo '<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=devprom" class="addthis_button_compact">Поделиться</a>';
			echo '<span class="addthis_separator">|</span>';
			echo '<a class="addthis_button_facebook"></a>';
			echo '<a class="addthis_button_myspace"></a>';
			echo '<a class="addthis_button_google"></a>';
			echo '<a class="addthis_button_twitter"></a>';
			echo '</div>';
			echo '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=devprom"></script>';
			$this->drawGreyBoxEnd();
		echo '</div>';
	}

	function drawFooter()
	{
		echo '<div id="footer">';
			echo '<div style="clear:both;width:100%;"></div>';
			echo '<br/>';

			echo '<div id="links">';
				echo '<ul style="float:right;">';
					echo '<li style="padding-right:0px;"><a href="/about/Пользовательское-соглашение">'.text('procloud514').'</a></li>';
				echo '</ul>';
			echo '</div>';
		echo '</div>';
	}
	
	function drawBody()
	{
	    echo '<script type="text/javascript">var email = '.JsonWrapper::encode($_REQUEST['Email']).'; </script>';
	    
		echo '<div id="pagecontent">';
			$this->content->draw();			
		echo '</div>';
	}
	
	function drawScripts()
	{
		$this->content->drawScripts();			
	}
	
	function validate()
	{
		if ( !$this->content->validate() )
		{
			//header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0

			$this->content = new CoPageNotFound;
			$this->content->setPage( $this );
		}
	}
	
	function draw()
	{
		global $project_it, $model_factory;
		
		$update = $model_factory->getObject('cms_Update');
		$update_it = $update->getFirst();
		 
		$current_version = $update_it->getDisplayName();
		
		$this->content = $this->getContent();
		$this->content->setPage( $this );
		
		$this->validate();
		
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/html; charset=windows-1251');

		$devprom_it = $this->getDevpromProjectIt();
		
 		echo '<html>';
 		echo '<head>';
 			echo '<meta name="keywords" content="'.$this->content->getKeywords().'" />';
			echo '<meta name="description" content="'.$this->content->getDescription().'" />';
 			echo '<title>'.$this->content->getTitle().'</title>';
 			echo '<link rel="stylesheet" type="text/css" href="/style?v='.$current_version.'"/>';
 			echo '<script type="text/javascript" src="/jscripts?v='.$current_version.'"></script>';
 		echo '</head>';

 		echo '<body>';
 		    echo '<div id="loginbg" style="position:relative;"></div>';

			$this->drawMenu();
			$this->drawBody();
			$this->drawFooter();

			$this->drawScripts();
			
			?>
			<script type="text/javascript">
	 			$(document).ready(function() { 
	 				<?php if ($_REQUEST['Email']) { ?> getJoinForm('/'); <?php } else { ?> getLoginForm('/'); <?php } ?>
	 				$("a.preview").fancybox({ 'hideOnContentClick': true });
	 			});
	 		</script> 
			<script type="text/javascript">
				var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
				document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
			</script>
			<script type="text/javascript">
			try {
				var pageTracker = _gat._getTracker("UA-10541243-4");
				pageTracker._trackPageview();
			} catch(err) {}
			</script>
			<?
		
 		echo '</body>';
 		echo '</html>';
	}
}

///////////////////////////////////////////////////////////////////////////////////
class CoPageIntro extends CoPage
{
	function drawMenu()
	{
		global $_REQUEST;
		
		echo '<div class="mainmenu">';
			echo '<div class="button" style="width:125px;">';
				echo '<div id="bd"><a href="/">'.translate('Облако проектов').'</a></div>';
			echo '</div>';
		echo '</div>';
	}
}

///////////////////////////////////////////////////////////////////////////////////
class CoPageLogged extends CoPage
{
	function drawMenu()
	{
		global $user_it, $model_factory;
		
		echo '<div id="loginbg"></div><div id="loginform"></div>';

		echo '<div class="mainmenu">';
			echo '<div class="button" style="width:125px;">';
				echo '<div id="bd"><a href="/main">'.translate('Облако проектов').'</a></div>';
			echo '</div>';

			echo '<div class="button" style="float:right;margin-right:20px;">';
				echo '<div id="bd">';
					echo '<a href="/profile" style="color:#87be22;">'.$user_it->getDisplayName().'</a>&nbsp;&nbsp;&nbsp;';
				echo '</div>';
			echo '</div>';

		echo '</div>';
	}
}

///////////////////////////////////////////////////////////////////////////////////
class CoPageContent
{
	var $page;
	
	function CoPageContent()
	{
		global $_REQUEST, $model_factory, $project_it;
		
		if ( $_REQUEST['project'] != '' )
		{
			$project = $model_factory->getObject('pm_Project');
			$project_it = $project->getByRefArray(
				array( 'LCASE(CodeName)' => strtolower($_REQUEST['project']) ) );
				
			if ( !$project_it->IsPublic() )
			{
				unset($project_it);
			}
		}
	}
	
	function validate()
	{
	}
	
	function getTitle()
	{
		global $project_it;

		if ( is_object($project_it) )
		{
			return $project_it->getDisplayName().' - '.translate('Облако проектов');
		}
		else
		{
			return translate('Облако проектов');
		}
	}
	
	function getDescription()
	{
		global $project_it;

		if ( is_object($project_it) )
		{
			return $project_it->get('Description');
		}
		else
		{
			return text('procloud567');
		}
	}
	
	function getKeywords()
	{
		global $model_factory, $project_it;
		
		if ( is_object($project_it) )
		{
			$tag = $model_factory->getObject('pm_ProjectTag');
			$tag_it = $tag->getByRef('Project', $project_it->getId());
			
			return join( $tag_it->fieldToArray('Caption'), ', ');
		}
		else
		{
			$words = array ( 
				translate('каталог'), 
				translate('проект'), 
				translate('управление'), 
				translate('хостинг'), 
				translate('проектный'), 
				translate('проекты'), 
				translate('продукт'), 
				translate('продукты'), 
				translate('devprom'), 
				translate('сайт'), 
				translate('участие'), 
				translate('тэги'), 
				translate('поиск'), 
				translate('команды'), 
				translate('найти'), 
				translate('участник'), 
				translate('команда'), 
				translate('облако'), 
				translate('разработка'), 
				translate('пользователь'), 
				translate('использовать') 
			);
			
			return join($words, ', ');
		}
	}

	function draw()
	{
	}
	
	function setPage ( $page )
	{
		$this->page = $page;
	}
	
	function getPage()
	{
		return $this->page;
	}
	
	function drawHeader( $title )
	{
		global $project_it;
		
		echo '<div style="float:left;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div style="padding-top:4px;">'.$title.'</div></div>';
				echo '<div id="rt">&nbsp;</div>';
				echo '<div id="an"></div>';
			echo '</div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';						
	}

	function drawHeaderWithTitle( $header, $title, $actions = null, $width = 'auto' )
	{
		global $project_it;
		
		echo '<div style="float:left;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div style="padding-top:4px;">'.$header.'</div></div>';
				echo '<div id="rt">&nbsp;</div>';
				echo '<div id="an"></div>';
			echo '</div>';
		echo '</div>';

		echo '<div style="float:left;padding-left:20px;padding-top:4px;">';
			echo '<h1 style="float:left;">'.$title.'</h1>';
		echo '</div>';
		
		if ( !is_null($actions) && count($actions) > 0 )
		{
			$this->drawActions( $actions, $width );
		}

		echo '<div style="clear:both;"></div>';
		echo '<br/>';						
	}

	function drawProjectHeader( $title, $pagebreak = true )
	{
		global $project_it;
		
		echo '<div style="float:left;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div style="padding-top:4px;"><a href="/main/'.$project_it->get('CodeName').'">'.$title.'</a></div></div>';
				echo '<div id="rt">&nbsp;</div>';
				echo '<div id="an"></div>';
			echo '</div>';
		echo '</div>';

		echo '<div style="float:left;padding-left:20px;padding-top:4px;">';
			echo '<h1 style="margin-top:0;"><a href="'.'/main/'.$project_it->get('CodeName').'">'.$project_it->getDisplayName().'</a>';
			if ( $project_it->HasProductSite() )
			{
				$url = CoController::getProductUrl($project_it->get('CodeName'));
				echo ' - <a href="'.$url.'">'.$url.'</a>';
			}
			echo '</h1>';
		echo '</div>';
		
		if ( $pagebreak )
		{
			echo '<div style="clear:both;"></div>';
			echo '<br/>';
		}						
	}
	
	function drawProjectHeaderWithActions( $title, $actions, $width = 'auto' )
	{
		$this->drawProjectHeader( $title, count($actions) < 1 );

		if ( count($actions) > 0 )
		{
			$this->drawActions( $actions, $width );
		}

		echo '<div style="clear:both;"></div>';
		echo '<br/>';						
	}
	
	function drawActions( $actions, $width = 'auto' )
	{
		echo '<div style="float:left;padding:10px 0 0 20px;">';
			echo '<div class="bmi_left"></div>';
			echo '<ul class="button_menu" style="float:left;">';
				echo '<li><a class="first" style="float:left;padding-left:10px;text-align:left;" href="'.$actions[0]['url'].'" title="'.$actions[0]['title'].'">'.$actions[0]['name'].'</a>';
					array_shift($actions);
					if ( count($actions) > 0 )
					{
	        			echo '<div style="clear:both;"></div><ul>';
	   					echo '<li class="disabled"><a href="javascript: return false;"></a></li>';
	
	    				foreach ( $actions as $action )
	    				{
	        				echo '<li><a style="text-align:left;padding-left:10px;" href="'.$action['url'].'" title="'.$action['title'].'">'.$action['name'].'</a></li>';
	    				}
	       				echo '<li class="disabled"><div class="bmbi_left"></div><a class="last" style="float:left;" href="javascript: return false;"></a><div class="bmbi_right"></div></li>';
	        			echo '</ul>';
					}
			    echo '</li>';
			echo '</ul>';				
			echo '<div class="bmi_right"></div>';
		echo '</div>';

		?>
		<script type="text/javascript">
			$('.button_menu > li').bind('mouseover', dropdown_open);
		    $('.button_menu > li').bind('mouseout',  dropdown_timer);
			document.onclick = dropdown_close;
		</script>
		<? 
	}

	function drawComments( $object_it )
	{
		global $project_it;
		
		echo '<a name="comment"></a>';

		echo '<div class="commentsholder" id="comments'.$object_it->getId().'">';
		echo '</div>';

		if ( is_object($project_it) )
		{
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				initComments('<? echo $project_it->get('CodeName')?>',
					'<? echo get_class($object_it->object) ?>', '<? echo $object_it->getId() ?>');
			});
		</script>
		<?
		}
		else
		{
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				initComments('0','<? echo get_class($object_it->object) ?>', '<? echo $object_it->getId() ?>');
			});
		</script>
		<?
		}
	}
	
	function drawPaging( $total_items, $limited_on_page )
	{
		global $_REQUEST;
		
		if ( $total_items > $limited_on_page )
		{
			if ( $_REQUEST['page'] != '' && $_REQUEST['page'] > 0 )
			{
				echo '<div style="float:left;">';
					$this->page->drawBlackButton('<a href="?page='.($_REQUEST['page'] - 1).'">'.translate('Предыдущая страница').'</a>');
				echo '</div>';
			}
	
			if ( $total_items > ($_REQUEST['page'] + 1) * $limited_on_page )
			{
				echo '<div style="float:right;">';
					$this->page->drawBlackButton('<a href="?page='.($_REQUEST['page'] + 1).'">'.translate('Следующая страница').'</a>');
				echo '</div>';
			}
		}
	}
	
	function drawScripts()
	{
	}
}

///////////////////////////////////////////////////////////////////////////////////
class CoPageNotFound extends CoPageContent
{
	function draw()
	{
		$page = $this->getPage();
		
		echo '<div style="float:left;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div style="padding-top:4px;">'.translate('Ошибка: 404').'</div></div>';
				echo '<div id="rt">&nbsp;</div>';
				echo '<div id="an">&nbsp;</div>';
			echo '</div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';						

		echo '<div class="description">';
			$page->drawWhiteBoxBegin();
			
			echo text('procloud568');
			
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

///////////////////////////////////////////////////////////////////////////////////
class CoInternalError extends CoPageContent
{
	function validate()
	{
		return true;
	}

	function draw()
	{
		$page = $this->getPage();
		
		echo '<div style="float:left;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div style="padding-top:4px;">'.translate('Ошибка: 500').'</div></div>';
				echo '<div id="rt">&nbsp;</div>';
				echo '<div id="an">&nbsp;</div>';
			echo '</div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';						

		echo '<div class="description">';
			$page->drawWhiteBoxBegin();
			
			echo text('procloud650');
			
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

?>