<?php

include_once "ObjectFactoryNotificator.php";
include SERVER_ROOT_PATH.'cms/c_mail.php';

class EmailNotificator extends ObjectFactoryNotificator
{
 	private $system_attributes = array();
	
	function __construct() 
	{
		parent::__construct();
	}
 	
 	function add( $object_it ) 
	{
		$this->process( 'add', $object_it, $object_it );
	}

 	function modify( $prev_object_it, $object_it ) 
	{
		$this->process( 'modify', $object_it, $prev_object_it );
	}

 	function delete( $object_it ) 
	{
		$this->process( 'delete', $object_it, $object_it );
	}
	
	function getMailBox( $object_it ) 
	{
		return new MailBox;
	}
	
	function process( $action, $object_it, $prev_object_it ) 
	{
		// указываем отправителя
		$from = $this->getSender($object_it, $action);
		if( $from == '' ) return false;

		// указываем получателей
		$recipients = $this->getRecipientArray(
			$object_it, $prev_object_it, $action);
			
		if( count($recipients) < 1 ) return false;

		$keys = array_keys($recipients);
		for($i = 0; $i < count($keys); $i++) 
		{
			$recipient = $recipients[$keys[$i]];
			
			$mail = $this->getMailBox( $object_it );
			$mail->setFrom($from);
			
			$mail->appendAddress( $this->getAddress($recipient) );

			// формируем заголовок письма
			$subject = $this->getSubject( $object_it, 
				$prev_object_it, $action, $recipient );
				
			$mail->setSubject( $subject );
	
			$body = $this->getBody( $action, 
				$object_it, $prev_object_it, $recipient );

			if ( $body != '' )
			{
				$mail->setBody($body);
				$mail->send();
			}
		}
		
		return true;
	}
	
	function getAddress( $recipient )
	{
		if ( is_object($recipient) )
		{
			return $recipient->getDisplayName().' <'.$recipient->get('Email').'>';
		}
		else
		{
			return $recipient;
		}
	}
	
	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		global $model_factory;
		
		$body = '';
		
		// вставляем значения атрибутов
		if(is_object($object_it->object->entity)) 
		{
			if($action == 'modify') 
			{
				$modified_body = '';
				$static_body = '';
				
		        $attributes = $object_it->object->getAttributesSorted();
                
				$attr_ref = array_keys($attributes);
				
		        for($i = 0; $i < count($attr_ref); $i++) 
				{
					$att_name = $attr_ref[$i];
        			
					if( !$this->isAttributeVisible($att_name, $object_it, $action) )
        			{
						continue;
					}

					$was_value = $this->getValue( $prev_object_it, $att_name );
					$now_value = $this->getValue( $object_it, $att_name );

        			if( $was_value != '' || $now_value != '' ) 
        			{
	       				if($object_it->object->IsReference($att_name)) 
	       				{
	       					$was_ref = $prev_object_it->getRef($att_name);
	       					$now_ref = $object_it->getRef($att_name);
	        					
							if($was_value != $now_value) 
							{
	       						$modified_body .= $object_it->object->getAttributeUserName($att_name).': '.$now_ref->getDisplayName();
	       						
	       						if ( $was_ref->getDisplayName() != '' )
	       						{
									$modified_body .= ' ('.translate('было').': '.$was_ref->getDisplayName().')';
	       						}
	       						
								$modified_body .= chr(10);
							}
							else
							{
								if( $this->isAttributeRequired($att_name, $object_it, $action) )
			        			{
		       						$static_body .= $object_it->object->getAttributeUserName($att_name).': '.$now_ref->getDisplayName();
									$static_body .= chr(10);
								}
							}
							
	       				} 
	       				else 
	       				{
							if($was_value != $now_value) 
							{
								if ( $att_type == 'text' )
								{
									if ( $now_value != '' )
									{
			       						$modified_body .= $object_it->object->getAttributeUserName($att_name).' ('.translate('текущее').'): '.
			       							$now_value.chr(10);
			       							
			       						if ( strlen($now_value) > 80 ) 
			       						{
			       							$modified_body .= chr(10);
			       						}
									}
		       						
		       						if ( $was_value != '' )
		       						{
										$modified_body .= 
											$object_it->object->getAttributeUserName($att_name).' ('.translate('было').'): '.
												$was_value.chr(10).chr(10);

			       						if ( strlen($was_value) > 80 ) 
			       						{
			       							$modified_body .= chr(10);
			       						}
		       						}
								}
								else
								{
									if ( $now_value != '' )
									{
		       							$modified_body .= $object_it->object->getAttributeUserName($att_name).': '.$now_value;
									}
		       						
		       						if ( $was_value != '' )
		       						{
										$modified_body .= ' ('.translate('было').': '.$was_value.')';
		       						}
		       						
									$modified_body .= chr(10);
								}
							}
							else
							{
								if( $this->isAttributeRequired($att_name, $object_it, $action) )
			        			{
		       						$static_body .= $object_it->object->getAttributeUserName($att_name).': '.$now_value;
									$static_body .= chr(10);
			        			}
							}
	       				}
        			}
                }
                
                if ( $modified_body != '' )
                {
	                $body = translate('Изменившиеся атрибуты').': '.Chr(10).Chr(10).
	                	$modified_body.Chr(10).Chr(10).
	                	translate('Остальные атрибуты').': '.Chr(10).Chr(10).
	                	$static_body.Chr(10).Chr(10);
                }
                else
                {
                	return $body;
                }
			}
		}
		
		if( $body == '' ) 
		{
			$body = $this->setAttributeValues($body, $object_it, $action);
		}
		
		return $body;
	}
	
	function getValue( $object_it, $attr )
	{
		$att_type = $object_it->object->getAttributeType($attr);

		if ( $att_type == 'file' )
		{
			return $object_it->getFileName($attr);
		}
		else
		{
			$value = $object_it->getHtmlDecoded($attr);
		}
		
		if ( $att_type == 'wysiwyg' )
		{ 
			$value = preg_replace('/\r|\n/', '', $value); 
		}
		
		if ( $value == 'N' )
		{
			$value = translate('Нет');
		}
		
		if ( $value == 'Y' )
		{
			$value = translate('Да');
		}

		return $value;
	}
	
	function setAttributeValues( $body, $object_it, $action ) 
	{
		global $model_factory;
		
        $attributes = $object_it->object->getAttributes();
        
		$attr_ref = array_keys($attributes);
		
        for($i = 0; $i < count($attr_ref); $i++) 
		{
			$att_name = $attr_ref[$i];
			
			if( !$this->isAttributeVisible($att_name, $object_it, $action) ) 
			{
				continue;
			}

			if( $object_it->get($att_name) != '' && $this->isAttributeRequired($att_name, $object_it, $action)) 
			{
	        	$body .= $object_it->object->getAttributeUserName($att_name).': ';
	        	
				if ( $object_it->object->IsReference($att_name) ) 
				{
					$ref = $object_it->getRef($att_name);
					$body .= $ref->getDisplayName().Chr(10);
				} 
				else 
				{
					$body .= $object_it->getHtmlDecoded($att_name).Chr(10);
				}
			}
        }

		return $body;
	}
	
	function getSender( $object_it, $action ) {
		return '';
	}

	function getRecipientArray( $object_it, $action ) {
		return array();
	}
	
	function getSubject( $object_it, $prev_object_it, $action, $recipient )
	{
		$uid = new ObjectUID;
		
		switch($action) 
		{
			case 'add':
				$subject = translate('Создание');
				break;
		
			case 'modify':
				$subject = translate('Изменение');
				break;
		
			case 'delete':
				$subject = translate('Удаление');
				break;
		}
		
		if ( $uid->hasUid($object_it) )
		{
			$subject .= ' ['.$uid->getObjectUid($object_it).']';
		}
		
		if( is_object($object_it->object->entity) ) 
		{
			$subject .= ', '.$object_it->object->getDisplayName().
				': '.html_entity_decode(substr($object_it->getDisplayName(), 0, 80), ENT_COMPAT | ENT_HTML401, 'cp1251');
		}
		
		return $subject;
	}
	
	function isAttributeVisible( $attribute_name, $object_it, $action )
	{
		switch ( $attribute_name )
		{
			case 'Password':
				return false;
			
			default:
				if ( $object_it->object->getAttributeType( $attribute_name ) == 'password' ) return false;
				
				if ( in_array($attribute_name, $this->getSystemAttributes($object_it)) ) return false;
				
				return $object_it->object->IsAttributeVisible( $attribute_name );
		}
	}

	function isAttributeRequired( $attribute_name, $object_it, $action )
	{
		switch ( $attribute_name )
		{
			default:	
				return true;
		}
	}
	
	protected function getSystemAttributes( $object_it )
	{
		if ( isset($this->system_attributes[get_class($object_it->object)]) )
		{
			return $this->system_attributes[get_class($object_it->object)];
		}
		
		return $this->system_attributes[get_class($object_it->object)] = $object_it->object->getAttributesByGroup('system');
	}
}