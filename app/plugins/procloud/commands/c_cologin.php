<?php

include_once "UserPasswordHashService.php";

class CoLogin extends CommandForm
{
 	function validate()
 	{
 		global $_REQUEST, $model_factory;
 		
		$this->command = new Login;
		
		if ( $_REQUEST['openid'] != '' )
		{
			$openid = $_REQUEST['openid'];
			$parts = preg_split('/http:\/\//', $openid);
			
			if ( count($parts) > 1 )
			{
				$openid = $parts[1]; 
			}

			$user = $model_factory->getObject('cms_User');
			$this->command->user_it = $user->getByRef('LCASE(Login)', strtolower($openid));
			
			if ( $this->command->user_it->count() > 0 )
			{
				if ( $this->command->user_it->IsBlocked() )
				{
					$this->replyError( $this->getResultDescription( 4 ) );
					return false;
				}

				$sql = " SELECT COUNT(1) cnt FROM cms_BlackList WHERE SystemUser = ".$this->command->user_it->getId();
				$it =  $this->command->user_it->object->createSQLIterator($sql);
				
				if ( $it->get('cnt') > 0 )
				{
					$this->replyError( $this->getResultDescription( 3 ) );
					return false;
				}
			}
		}
		else
		{
			$result = $this->command->validate();
		
			if( $result > 0 ) 
			{
				$this->replyError( $this->getResultDescription( $result ) );
				return false;
			}
		}

		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST;
		
		if ( $_REQUEST['openid'] != '' )
		{
			$this->checkOpenId();
		}
		else
		{
			$user_it = $this->command->getUserIt();
			
			if ( $_REQUEST['pass'] != '' )
			{
				$service = new UserPasswordHashService();
				
				$service->storePassword($user_it, $service->getHash($_REQUEST['pass'], $_REQUEST['lru']));
			}
			
			$session = getSession();
			
			$session->open( $user_it );
						
			$this->replySuccess( $this->getResultDescription( -1 ) );
		}
	}
	
	function getResultDescription( $result )
	{
		if ( $result == -1 ) return text('procloud508');
		
		switch ( $result )
		{
			case 2000:
				return text('procloud621');			
		}
		
		return $this->command->getResultDescription( $result );
	}
	
	function checkOpenId()
	{
		global $_REQUEST;

		$openid = strtolower($_REQUEST['openid']);
		if ( strpos($openid, 'http://') === false && strpos($openid, 'https://') === false )
		{
	 		$openid = 'http://'.$openid;
	 	}
		
		$curl = curl_init($openid);

		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		if ( curl_errno($curl) != 0 )
		{
			$this->replyError( $openid.'<br/>'.str_replace('%1', curl_error($curl), text('procloud622')) );
		}
		
		list($servers, $delegates) = $this->parseOpenIdResponse( $response );

		if ( count($servers) < 1 )
		{
			$this->replyError( $openid.'<br/>'.$this->getResultDescription( 2000 ) );
		}
		
		$patterns = array();
		$trusted_url = 'http://'.$_SERVER['SERVER_NAME'];
		
		
		$response_url = $trusted_url.'/openid?';
		$response_url .= (array_key_exists('rememberopenid', $_REQUEST) ? 'remember=on&' : '' );
		$response_url .= (array_key_exists('lru', $_REQUEST) ? 'loginredir='.urlencode($_REQUEST['lru']).'&' : '');
		$response_url .= (preg_match('/getPostComment\(([\d]+)\)/i', $_REQUEST['lrs'], $patterns) ? 'replycomment='.$patterns[1].'&' : '');

		$params = array();
		$params['openid.return_to'] = urlencode($response_url);
		$params['openid.mode'] = 'checkid_setup';
		$params['openid.identity'] = urlencode($openid);
		$params['openid.trust_root'] = urlencode($trusted_url);
		$params['openid.sreg.required'] = urlencode('email,nickname');
		$params['openid.sreg.policy_url'] = $trusted_url;

		$parts = array();
		foreach ( array_keys($params) as $key )
		{
			array_push($parts, $key.'='.$params[$key]);
		}

		$this->replyRedirect( $servers[0].(strpos($servers[0], '?') !== false ? '&' : '?').join('&', $parts) );
	}
	
	function parseOpenIdResponse( $content )
	{
		preg_match_all('/<link[^>]*rel=[\'"]openid.server[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches1);
		preg_match_all('/<link[^>]*href=\'"([^\'"]+)[\'"][^>]*rel=[\'"]openid.server[\'"][^>]*\/?>/i', $content, $matches2);
		$servers = array_merge($matches1[1], $matches2[1]);
		
		if ( count($servers) < 1 )
		{
			preg_match_all('/<link[^>]*rel=[\'"][^\'"]*openid2.provider[^\'"]*[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches1);
			preg_match_all('/<link[^>]*href=\'"([^\'"]+)[\'"][^>]*rel=[\'"][^\'"]*openid2.provider[^\'"]*[\'"][^>]*\/?>/i', $content, $matches2);
			$servers = array_merge($matches1[1], $matches2[1]);
		}

		preg_match_all('/<link[^>]*rel=[\'"]openid.delegate[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches1);
		preg_match_all('/<link[^>]*href=[\'"]([^\'"]+)[\'"][^>]*rel=[\'"]openid.delegate[\'"][^>]*\/?>/i', $content, $matches2);
		$delegates = array_merge($matches1[1], $matches2[1]);
		
		return array($servers, $delegates);
	}
}
