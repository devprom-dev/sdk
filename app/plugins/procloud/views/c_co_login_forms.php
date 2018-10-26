<?php

/////////////////////////////////////////////////////////////////////////////////
class CoLoginController
{
	function setPage( $page )
	{
	}
	
	function validate()
	{
		global $_REQUEST, $model_factory, $user_it;

		if ( $_REQUEST['action'] == 'openid' )
		{
			if ( $_REQUEST['openid_mode'] == 'id_res' && $this->checkOpenId() )
			{
				$user_login = $_REQUEST['openid_identity'];
				
				$parts = preg_split('/http:\/\//', $user_login);
				if ( count($parts) > 1 )
				{
					$user_login = $parts[1]; 
				}
				
				$user = $model_factory->getObject('cms_User');
				$user_it = $user->getByRef("LCASE(Login)", strtolower($user_login));
				
				if ( $user_it->count() < 1 )
				{
				 	$user_id = $user->add_parms(
				 		array(
				 			'Email' => '',
				 			'Caption' => $user_login,
				 			'Login' => $user_login,
				 			'Password' => '',
				 			'Language' => 1
				 			)
				 		);
				 		
				 	$user_it = $user->getExact($user_id);
				}
		
				$session = getSession();
				$session->open( $user_it );

				exit(header('Location: '.($_REQUEST['loginredir'] != '' ? $_REQUEST['loginredir'] : '/')));
			}
			else
			{
				return false;
			}
		}
		
		$formmap = array(
			'login' => 'CoLoginForm',
			'join' => 'CoJoinForm',
			'joinsimple' => 'CoJoinSimpleForm',
			'download' => 'CoAuthorizedDownloadForm',
			'restore' => 'CoRestoreForm',
			'restorerequest' => 'CoRequestToRestoreForm',
		);
		
		if ( !array_key_exists($_REQUEST['action'], $formmap) )
		{
			return false;
		}
		else
		{
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			header("Content-Type: text/html; charset=windows-1251");
	
			$form = new $formmap[$_REQUEST['action']]($model_factory->getObject('cms_User'));
			$form->draw();
		}
		
		die();
	}
	
	function checkOpenId()
	{
		global $_REQUEST;
		
		$openid = $_REQUEST['openid_identity'];
		
		if ( $openid == '' ) 
		{
			return false;
		}
		
		$curl = curl_init( $openid );
		
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		if ( curl_errno($curl) != 0 )
		{
			return false;
		}
		
		list($servers, $delegates) = $this->parseOpenIdResponse( $response );
		if ( count($servers) < 1 )
		{
			return false;
		}

		$params = array (
			'openid.assoc_handle' => urlencode($_REQUEST['openid_assoc_handle']),
			'openid.signed' => urlencode($_REQUEST['openid_signed']),
			'openid.sig' => urlencode($_REQUEST['openid_sig'])
		);
		
		$arr_signed = explode(",",str_replace('sreg.','sreg_',$_REQUEST['openid_signed']));
		
		for ( $i=0; $i < count($arr_signed); $i++ )
		{
			$s = str_replace('sreg_','sreg.', $arr_signed[$i]);
			$c = $_REQUEST['openid_' . $arr_signed[$i]];
			$params['openid.'.$s] = urlencode($c);
		}
		
		$params['openid.mode'] = "check_authentication";

		$parts = array();
		foreach ( array_keys($params) as $key )
		{
			array_push($parts, $key.'='.$params[$key]);
		}

		$curl = curl_init($servers[0]);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPGET, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, join('&', $parts));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		if ( curl_errno($curl) != 0 )
		{
			return false;
		}

		$r = array();
		$response = explode("\n", $response);
		
		foreach ( $response as $line )
		{
			$line = trim($line);
			if ( $line != "" )
			{
				list($key, $value) = explode(":", $line, 2);
				$r[trim($key)] = trim($value);
			}
		}
		
		if ( $r['is_valid'] == "true" )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function parseOpenIdResponse( $content )
	{
		preg_match_all('/<link[^>]*rel=[\'"]openid.server[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches1);
		preg_match_all('/<link[^>]*href=\'"([^\'"]+)[\'"][^>]*rel=[\'"]openid.server[\'"][^>]*\/?>/i', $content, $matches2);
		$servers = array_merge($matches1[1], $matches2[1]);
		
		preg_match_all('/<link[^>]*rel=[\'"]openid.delegate[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches1);
		preg_match_all('/<link[^>]*href=[\'"]([^\'"]+)[\'"][^>]*rel=[\'"]openid.delegate[\'"][^>]*\/?>/i', $content, $matches2);
		$delegates = array_merge($matches1[1], $matches2[1]);
		
		return array($servers, $delegates);
	}
}

////////////////////////////////////////////////////////////////////////////////
class CoLoginForm extends CoPageForm
{
 	function getAddCaption()
 	{
 		return translate('Авторизация');
 	}

 	function getCommandClass()
 	{
 		return 'cologin';
 	}
 	
	function getAttributes()
	{
		$attrs = array ( 'openid', 'login', 'pass', 'more' );
    	return $attrs;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'openid':
				return 'text';

			case 'login':
				return 'text';
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return false;
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'login':
				return translate('Логин');

			case 'pass':
				return translate('Пароль'); 	

			case 'openid':
				$imgs = array(
					'<a class="oid" href="javascript: startopenid(\'.livejournal.com\');" title="Live Journal OpenId"><img src="/images/oidlj.png"></a>',
					'<a class="oid" href="javascript: startopenid(\'.ya.ru\');" title="Yandex.ru OpenId"><img src="/images/oidya.png"></a>',
				);
			
				return 'OpenID &nbsp; '.join('&nbsp;', $imgs);

			default:
				return parent::getName( $attribute );
				
		}
	}

 	function getButtonText()
 	{
 		return translate('Войти');
 	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		global $tab_index;
		
		if ( $attribute == 'more' )
		{
			echo '<div style="border-bottom:3px solid silver;padding-top:5px;clear:both;"></div>';
			
			echo '<div style="padding-top:15px;">';
				echo text('procloud620');
			echo '</div>';
			
			$tab_index++;
		}
		else if ( $attribute == 'pass' )
		{
			echo '<input class=input_value type="password" id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'" tabindex="'.$tab_index.'">';

			echo '<div style="float:left;padding-top:3px;">';
				echo '<input id="remember" class="check" type="checkbox" name="remember" checked >' .
					'<label style="float:left;">'.translate('Запомнить').'</label>';
			echo '</div>';
			
			echo '<div style="float:right;padding-top:3px;">';
				echo '<a href="javascript: getRestoreRequestForm();">'.translate('Восстановить пароль').'</a>';
			echo '</div>';
		}
		else if ( $attribute == 'openid' )
		{
			echo '<input class=input_value id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'" tabindex="'.$tab_index.'">';

			echo '<div style="float:left;padding-top:3px;">';
				echo '<input id="remember" class="check" type="checkbox" name="rememberopenid" checked >' .
					'<label style="float:left;">'.translate('Запомнить').'</label>';
			echo '</div>';

			echo '<div style="border-bottom:3px solid silver;padding-top:15px;clear:both;"></div>';
		}
		else
		{
			return parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}

	function draw()
	{
		$form_processor_url = '/command/'.$this->getCommandClass();
	
		$this->drawScript();
		
		echo '<div style="margin:24px;">';
			echo '<form id="myForm" action="'.$form_processor_url.'" method="post" style="width:100%;" onsubmit="javascript: return false;">';
				echo '<input type="hidden" id="action" name="action" value="'.$this->getAction().'">';
				echo '<input type="hidden" name="MAX_FILE_SIZE" value="1048576">';
				echo '<input type="hidden" id="lru" name="lru" value="">';
				echo '<input type="hidden" id="lrs" name="lrs" value="">';
				echo '<table style="width:100%;">';
				$attributes = $this->getAttributes();
		
				for ( $i = 0; $i < count($attributes); $i++ )
				{
					$this->drawAttribute( $attributes[$i] );
				}
				echo '</table>';
			echo '</form>';
			
			echo '<div id="result" style="clear:both;padding-bottom:12px;"></div>';

			if ( $_SERVER['HTTP_HOST'] == 'devprom.ru' )
			{
    			echo '<div id="buttons" style="width:100%;">';
    				echo '<div class="blackbutton" id="submitbutton" style="padding-right:12px;">';
    					echo '<div id="body" style="width:80px;">';
    						echo '<a id="submit" href="javascript: '.$this->getSubmitScript().'">'.$this->getButtonText().'</a>';
    					echo '</div>';
    					echo '<div id="rt"></div>';
    				echo '</div>';
    	
    				echo '<div class="blackbutton" id="closebutton">';
    					echo '<div id="body" style="width:80px;">';
    						echo '<a id="close" href="javascript: closeLoginForm();">'.translate('Закрыть').'</a>';
    					echo '</div>';
    					echo '<div id="rt"></div>';
    				echo '</div>';
    			echo '</div>';
			}
			else
			{
				echo '<div id="grbutton" style="width:180px;">';
				    echo '<div id="lt"></div>';
					echo '<div id="bd" style="width:145px;">';
						echo '<a style="line-height:35px;font-size:18px;" id="submit" href="javascript: '.$this->getSubmitScript().'">'.$this->getButtonText().'</a>';
					echo '</div>';
					echo '<div id="rt"></div>';
				echo '</div>';
			}
				
			echo '<div style="clear:both;"></div>';

		echo '</div>';
	}

	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', refreshWindow)';
	}
}

////////////////////////////////////////////////////////////////////////////////
class CoJoinForm extends CoLoginForm
{
 	var $question_it;
 	
 	function CoJoinForm ( $object )
 	{
 		global $model_factory;
 		
		$question = $model_factory->getObject('cms_CheckQuestion');
		$this->question_it = $question->getRandom();
		
		parent::CoPageForm( $object );
 	}

 	function getAddCaption()
 	{
 		return translate('Регистрация');
 	}

 	function getCommandClass()
 	{
 		return 'cojoin';
 	}
 	
	function getAttributes()
	{
		$attrs = array ( 'Login', 'Email', 'Password', 'Password2', 'Question', 'more' );
    	return $attrs;
	}

	function getAttributeValue( $attribute )
	{
		global $_REQUEST;
		
		switch ( $attribute )
		{
			case 'Login':
				if ( $_REQUEST['Email'] != '' )
				{
					$parts = preg_split('/@/', $_REQUEST['Email']);
					return $parts[0];
				}
				else 
				{
					return parent::getAttributeValue( $attribute );
				}
				
			case 'Email':
				if ( $_REQUEST[$attribute] != '' )
				{
					return $_REQUEST[$attribute];
				}
				else 
				{
					return parent::getAttributeValue( $attribute );
				}
				
			default:
				return parent::getAttributeValue( $attribute ); 	
		}
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Login':
			case 'Email':
			case 'Question':
				return 'text';
				
			case 'Password':
			case 'Password2':
				return 'password'; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return false;
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Login':
				return translate('Логин');

			case 'Email':
				return translate('E-mail');

			case 'Password':
				return translate('Пароль');

			case 'Password2':
				return translate('Повтор пароля');

			case 'Question':
				return $this->question_it->getDisplayName(); 	

			default:
				return parent::getName( $attribute );
		}
	}

 	function getButtonText()
 	{
 		return translate('Войти');
 	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		global $tab_index;
		
		if ( $attribute == 'more' )
		{
			$script = "$('#agr').val( $('#chkb').is(':checked') ? 'yes' : 'no' );";
			
			echo '<div id="agreementField">' .
				'<input type="checkbox" id="chkb" class="check" onchange="'.$script.'">' .
					'<label style="float:left;">'.
						text('procloud1003').'</label></div>';
			
			echo '<input type="hidden" id="agr" name="Agreement" value="">';
			
			$tab_index++;						
		}
		else if ( $attribute == 'Question' )
		{
			?>
			<input class=input_value name="QAnswer" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>">
			<input type="hidden" name="QHash" value="<? echo $this->question_it->getHash(); ?>">
			<?	
			
			$tab_index++;						
		}
		else
		{
			return parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}

	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', refreshWindow)';
	}
}

////////////////////////////////////////////////////////////////////////////////
class CoJoinSimpleForm extends CoJoinForm
{
 	function getCommandClass()
 	{
 		return 'cojoin&skip=info';
 	}

	function getSubmitScript()
	{
		return CoLoginForm::getSubmitScript();
	}
}

////////////////////////////////////////////////////////////////////////////////
class CoAuthorizedDownloadForm extends CoPageForm
{
	function draw()
	{
		echo '<div id="myForm" style="margin:24px;">';
			echo '<div width="100%">';
				echo '<div>';
					echo text('procloud524');
				echo '</div>';

				echo '<br/>';
								
				echo '<div>';
					echo '<a href="javascript: getLoginForm();"><b>'.translate('Авторизоваться').'</b></a> - '.text('procloud525');
				echo '</div>';

				echo '<br/>';
				echo '<br/>';

				echo '<div>';
					echo '<a href="javascript: getJoinSimpleForm();"><b>'.translate('Зарегистрироваться').'</b></a> - '.text('procloud526');
				echo '</div>';

				echo '<br/>';
			echo '</div>';

			echo '<br/>';
			echo '<br/>';

			echo '<div id="buttons" style="width:100%;">';
				echo '<div class="blackbutton" id="closebutton">';
					echo '<div id="body" style="width:80px;">';
						echo '<a href="javascript: closeLoginForm();">'.translate('Закрыть').'</a>';
					echo '</div>';
					echo '<div id="rt"></div>';
				echo '</div>';
			echo '</div>';
				
			echo '<div style="clear:both;"></div>';

		echo '</div>';
	}
}

////////////////////////////////////////////////////////////////////////////////
class CoRequestToRestoreForm extends CoLoginForm
{
 	function getAddCaption()
 	{
 		return translate('Запрос на восстановление пароля');
 	}

 	function getCommandClass()
 	{
 		return 'corestorerequest';
 	}
 	
	function getAttributes()
	{
		return array ( 'Email' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Email':
				return 'text';
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return false;
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Email':
				return translate('Введите ваш Email');

			default:
				return parent::getName( $attribute );
		}
	}

 	function getButtonText()
 	{
 		return translate('Продолжить');
 	}

	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', prepareToRestore)';
	}
}

////////////////////////////////////////////////////////////////////////////////
class CoRestoreForm extends CoLoginForm
{
 	function getAddCaption()
 	{
 		return translate('Восстановление пароля');
 	}

 	function getCommandClass()
 	{
 		return 'corestore';
 	}
 	
	function getAttributes()
	{
		return array ( 'Key', 'Password', 'Password2' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Key':
				return 'text';
				
			case 'Password':
			case 'Password2':
				return 'password'; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return false;
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Key':
				return translate('Ключ для сброса пароля');

			case 'Password':
				return translate('Новый пароль');

			case 'Password2':
				return translate('Повтор нового пароля');

			default:
				return parent::getName( $attribute );
		}
	}

 	function getButtonText()
 	{
 		return translate('Отправить');
 	}

	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', closeLoginForm)';
	}
}

?>