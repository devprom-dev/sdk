<?php

/////////////////////////////////////////////////////////////////////////////////
class CoCreatePageContent extends CoPageContent
{
	function validate()
	{
		global $_REQUEST, $model_factory, $user_it;
		
		return true;
	}
	
	function draw()
	{
		global $model_factory, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		
		// introduction
		echo '<div style="width:100%;float:left;">';

			echo '<div style="float:left;">';
				echo '<div id="grbutton" style="width:220px;">';
					echo '<div id="lt">&nbsp;</div>';
					echo '<div id="bd"><div style="padding-top:4px;"><a href="/room">'.translate('Мои проекты').'</a></div></div>';
					echo '<div id="rt">&nbsp;</div>';
					echo '<div id="an"></div>';
				echo '</div>';
			echo '</div>';

			echo '<div style="clear:both;"></div>';
			echo '<br/>';						
			
			echo '<div style="float:left;width:100%;">';
				if ( $_REQUEST['action'] == 'createproject' )
				{
					$this->drawCreateProjectForm();
				}
				else if ( $_REQUEST['action'] == 'createsite' )
				{
					$this->drawCreateSiteForm();
				}
				else if ( $_REQUEST['action'] == 'createfeedback' )
				{
					$this->drawCreateFeedbackForm();
				}
				else if ( $_REQUEST['action'] == 'remove' )
				{
					$this->drawRemoveForm();
				}
				else if ( $_REQUEST['action'] == 'activateproject' )
				{
					$this->drawActivateProjectForm();
				}
				else if ( $_REQUEST['action'] == 'projectremove' )
				{
					$this->drawRemoveConfirmForm();
				}
				echo '</div>';

		echo '<div style="clear:both;">&nbsp;</div>';
		echo '<br/>';
		
		echo '</div>';
	}

	function drawCreateProjectForm()
	{
		global $model_factory, $user_it;
		
		$page = $this->getPage();
		$form = new CoProjectCreateForm( $model_factory->getObject('pm_Project') );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			if ( !$user_it->IsReal() )
			{
				echo text('procloud518');
			}
			else
			{
				echo '<div style="width:50%;float:left;">';
					$form->draw();
				echo '</div>';
			}
			
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}

	function drawActivateProjectForm()
	{
		global $model_factory, $user_it;
		
		$page = $this->getPage();
		$form = new CoActivateProjectForm( $model_factory->getObject('pm_Project') );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			if ( !$user_it->IsReal() )
			{
				echo text('procloud518');
			}
			else
			{
				echo '<div style="width:50%;float:left;">';
					$form->draw();
				echo '</div>';
			}
			
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
	
	function drawCreateSiteForm()
	{
		global $model_factory, $user_it;
		
		$page = $this->getPage();
		$form = new CoSiteCreateForm( $model_factory->getObject('pm_Project') );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			if ( !$user_it->IsReal() )
			{
				echo text('procloud518');
			}
			else
			{
				echo '<div style="width:60%;">';
					$form->draw();
				echo '</div>';
			}

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}

	function drawCreateFeedbackForm()
	{
		global $model_factory, $user_it;
		
		$page = $this->getPage();
		$form = new CoFeedbackCreateForm( $model_factory->getObject('pm_Project') );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			if ( !$user_it->IsReal() )
			{
				echo text('procloud518');
			}
			else
			{
				echo '<div style="width:60%;float:left;">';
					$form->draw();
				echo '</div>';

				echo '<div style="width:38%;float:right;">';
					echo '<div class="note" style="display:none;">';
						echo text('procloud574').'<br/><br/>';

						echo htmlspecialchars('<script src="http://projectscloud.ru/scripts/jquery/jquery.pack.js" language="javascript" type="text/javascript"></script>').'<br/>';
						echo htmlspecialchars('<script src="http://projectscloud.ru/feedback/111" language="javascript" type="text/javascript" charset="windows-1251"></script>').'<br/>';

						echo '<br/>'.text('procloud575').'<br/><br/>';

						echo htmlspecialchars('<script type="text/javascript">').'<br/>';
						echo htmlspecialchars('feedbackOpts.tagBackground = "red";').'<br/>';
						echo htmlspecialchars('feedbackOpts.formBackground = "red";').'<br/>';
						echo htmlspecialchars('</script>').'<br/>';
						
						echo '<br/>'.text('procloud576').'<br/>';
						
					echo '</div>';
				echo '</div>';
			}

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}

	function drawRemoveForm()
	{
		global $model_factory, $user_it;
		
		$page = $this->getPage();
		$form = new CoRemoveForm( $model_factory->getObject('pm_Project') );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			if ( $user_it->IsReal() )
			{
				echo '<div style="width:50%;">';
					$form->draw();
				echo '</div>';
			}

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}

	function drawRemoveConfirmForm()
	{
		global $model_factory, $user_it;
		
		$page = $this->getPage();
		$form = new CoRemoveConfirmForm( $model_factory->getObject('pm_Project') );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			if ( $user_it->IsReal() )
			{
				echo '<div style="width:50%;">';
					$form->draw();
				echo '</div>';
			}

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

 ////////////////////////////////////////////////////////////////////////////////
 class CoProjectCreateForm extends CoPageForm
 {
 	var $question_it;
 	
 	function CoProjectCreateForm ( $object )
 	{
 		global $model_factory;
 		
		$question = $model_factory->getObject('cms_CheckQuestion');
		$this->question_it = $question->getRandom();
		
		parent::CoPageForm( $object );
 	}

 	function getAddCaption()
 	{
 		return translate('Создание нового проекта');
 	}

 	function getCommandClass()
 	{
 		return 'cocreateproject';
 	}
 	
	function getAttributes()
	{
		$config = getConfiguration();
		
		$attrs = array ( 'Codename', 'Caption', 'Template', 'Question' );
		
    	return $attrs;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Codename':
			case 'Caption':
			case 'Access':
			case 'Language':
				return 'text'; 	

			case 'Template':
				return 'object';
		}
	}

	function getAttributeClass( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Language':
				return $model_factory->getObject('cms_Language');

			case 'Template':
				return $model_factory->getObject('pm_ProjectTemplate');
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
			case 'Codename':
				return translate('Кодовое название проекта');

			case 'Caption':
				return translate('Название проекта');

			case 'Template':
				return translate('Шаблон проекта');

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
			case 'Codename':
				return str_replace('%1', _getServerUrl(), text('procloud479'));

			case 'Caption':
				return text('procloud480');

			case 'Template':
				return text('procloud741');

 			case 'Question':
 				return text('procloud456');
 		}
 	}

	function getButtonText()
	{
		return translate('Создать проект');
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

	function getSubmitScript()
	{
		return "submitForm('".$this->getAction()."', function(){ $('.note').show(); } )";
	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class CoActivateProjectForm extends CoPageForm
 {
 	function getAddCaption()
 	{
 		return translate('Активация нового проекта');
 	}

 	function getCommandClass()
 	{
 		return 'activateproject';
 	}
 	
	function getAttributes()
	{
		return array ( 'key' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'key':
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
	
	function getAttributeValue( $attribute )
	{
		global $_REQUEST;
		
		switch ( $attribute )
		{
			case 'key':
				return $_REQUEST[$attribute]; 	
		}
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'key':
				return translate('Ключ активации');
		}
	}

	function getButtonText()
	{
		return translate('Создать проект');
	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class CoRemoveForm extends CoPageForm
 {
 	function getAddCaption()
 	{
 		return translate('Удаление проекта');
 	}

 	function getCommandClass()
 	{
 		return 'coremoveproject';
 	}
 	
	function getAttributes()
	{
		return array('codename'); 	
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'codename':
				return translate('Название проекта'); 	
		}
	}

	function IsAttributeRequired( $attribute )
	{
		switch ( $attribute )
		{
			case 'codename':
				return true; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		global $_REQUEST;
		
		switch ( $attribute )
		{
			case 'codename':
				return true;
		}
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'codename':
				return 'dictionary'; 	
		}
	}

	function getAttributeClass( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'codename':
				return $model_factory->getObject('MyProject'); 	
		}
	}

 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
 			case 'codename':
 				return text('procloud291');
 		}
 	}

	function getButtonText()
	{
		return translate('Продолжить');
	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class CoRemoveConfirmForm extends CoPageForm
 {
 	function getAddCaption()
 	{
 		return translate('Подтверждение операции удаления проекта');
 	}

 	function getCommandClass()
 	{
 		return 'projectremove';
 	}
 	
	function getAttributes()
	{
		return array ( 'key' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'key':
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
	
	function getAttributeValue( $attribute )
	{
		global $_REQUEST;
		
		switch ( $attribute )
		{
			case 'key':
				return $_REQUEST[$attribute]; 	
		}
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'key':
				return translate('Ключ подтверждения операции');
		}
	}

	function getButtonText()
	{
		return translate('Удалить');
	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class CoSiteCreateForm extends CoPageForm
 {
 	var $question_it;
 	
 	function CoSiteCreateForm ( $object )
 	{
 		global $model_factory;
 		
		$question = $model_factory->getObject('cms_CheckQuestion');
		$this->question_it = $question->getRandom();
		
		parent::CoPageForm( $object );
 	}
 	
 	function getAddCaption()
 	{
 		return translate('Создание сайта продукта');
 	}

 	function getCommandClass()
 	{
 		return 'cocreatesite';
 	}
 	
	function getAttributes()
	{
		return array( 'CodeName', 'Project', 'Template', 'Question' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Login':
			case 'Email':
			case 'CodeName':
			case 'Project':
			case 'Question':
			case 'Template':
				return 'text'; 	

			case 'Password':
			case 'PasswordRepeat':
				return 'password';

			case 'Conditions':
				return 'char';

			case 'Language':
				return 'object'; 	
		}
	}

	function getAttributeClass( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Language':
				return $model_factory->getObject('cms_Language');
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
			case 'CodeName':
				return translate('Кодовое название продукта');

			case 'Project':
				return translate('Название продукта');

			case 'Template':
				return translate('Шаблон сайта продукта');

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
 			case 'CodeName':
 				return text('procloud454');

 			case 'Project':
 				return text('procloud455');

 			case 'Question':
 				return text('procloud456');

 			case 'Template':
 				return text('procloud475');
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
		elseif ( $attribute == 'Template' )
		{
			$templates = CoController::getTemplates();
			
			foreach ( $templates as $template )
			{
				echo '<div style="float:left;padding-right:15px;padding-bottom:12px;">';
					echo '<input type="radio" style="width:18px;" name="'.$attribute.'" value="'.$template.'">' .
						'<label><img src="/procloud/templates/mini/images/'.$template.'.png"></label></input>';
				echo '</div>';
			}
			
			echo '<div style="clear:both;">';
			echo '</div>';
			
			$tab_index++;						
		}
		else
		{
			parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}

	function getButtonText()
	{
		return translate('Создать сайт');
	}
 }
  
 ////////////////////////////////////////////////////////////////////////////////
 class CoFeedbackCreateForm extends CoProjectCreateForm
 {
 	function getAddCaption()
 	{
 		return translate('Создание формы для отзывов');
 	}

 	function getCommandClass()
 	{
 		return 'cocreatefeedback';
 	}
 	
	function getAttributes()
	{
		return array( 'Codename', 'Caption', 'Question' );
	}

	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Codename':
				return translate('Кодовое название проекта');

			case 'Caption':
				return translate('Название проекта');

			default:
				return parent::getName( $attribute );
				
		}
	}

 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
 			case 'Codename':
 				return text('procloud572');

 			case 'Caption':
 				return text('procloud573');

 			default:
 				return parent::getDescription( $attribute );
 		}
 	}

	function getButtonText()
	{
		return translate('Создать проект');
	}

	function getSubmitScript()
	{
		return "submitForm('".$this->getAction()."', function(){ $('.note').html($('.note').html().replace('111', $('#Codename').val())); $('.note').show(); } )";
	}
 }

?>
