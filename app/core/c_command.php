<?php

 ////////////////////////////////////////////////////////////////////////////
 class Command
 {
 	var $log;
 	
 	function Command()
 	{
 		try 
 		{
 			$this->log = Logger::getLogger('Commands');
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 		}
 	}
 	
 	function getLogger()
 	{
 		return $this->log;
 	}
 	
	function logStart()
	{
		$log = $this->getLogger();
		if ( !is_object($log) ) return; 
		
		$log->info( str_replace('%1', get_class($this), text(1206)) );
	}
	
	function logFinish()
	{
		$log = $this->getLogger();
		if ( !is_object($log) ) return; 
		
		$log->info( str_replace('%1', get_class($this), text(1207)) );
	}
	
	function logParms( $parms )
	{
		$log = $this->getLogger();
		if ( !is_object($log) ) return; 
		
		$log->info( str_replace('%1', 
			str_replace(chr(10), '', 
				str_replace(chr(13), '', var_export($parms, true))),
					text('1209')) );
	}
	
	function logBatchParms( $parms )
	{
		$log = $this->getLogger();
		if ( !is_object($log) ) return;
		 
		$log->info( str_replace('%1', $parms, text(1210)) );
	}
	
	function execute()
 	{
 	}
 	
	function replyError( $message )
	{
		$log = $this->getLogger();
		if ( is_object($log) ) $log->error( $message );
		
		$this->replyResult( true, $message );
	}
	
	function replySuccess( $message = '' )
	{
		$log = $this->getLogger();
		if ( is_object($log) ) $log->info( $message );
		
		$this->replyResult( false, $message );

	}

	function replyResult( $is_error, $message )
	{
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/xml; charset='.APP_ENCODING);

		echo '<reply>';
			echo '<state>';
				echo ($is_error ? 'error' : 'success');
			echo '</state>';
			echo '<text>';
				echo htmlspecialchars($message, ENT_COMPAT | ENT_HTML401, APP_ENCODING);
			echo '</text>';
		echo '</reply>';
		
		exit();		
	}

	function Utf8ToWin($fcontents) 
	{
		return IteratorBase::utf8towin($fcontents);
    }
	
	function wintoutf8($s) 
 	{
 		return IteratorBase::wintoutf8($s);
 	}   
 }

 define ('CO_ACTION_CREATE', 1);
 define ('CO_ACTION_MODIFY', 2);
 define ('CO_ACTION_DELETE', 3);
 define ('CO_ACTION_PREVIEW', 4);

 ////////////////////////////////////////////////////////////////////////////
 class CommandForm extends Command
 {
 	function checkRequired( $fields )
 	{
 		global $_REQUEST;
 		
 		for( $i = 0; $i < count($fields); $i++ )
 		{
 			if ( trim($_REQUEST[$fields[$i]]) == '' )
 			{
 				$this->replyError( $this->getResultDescription( 1 ) );
 			}
 		}
 	}

 	function checkUnique( $object, $key, $key2 = null )
 	{
 		global $_REQUEST;

		if ( is_null($key2) )
		{
	 		$it = $object->getByRefArray( 
	 			array($key => $_REQUEST[$key] )
	 			);
		}
		else
		{
	 		$it = $object->getByRefArray( 
	 			array( $key => $_REQUEST[$key], $key2 => $_REQUEST[$key2] )
	 			);
		}

 		if ( $it->count() > 0 )
 		{
 			$this->error_object = $object;
 			
 			if ( is_null($key2) )
 			{
	 			$this->error_fields = array ( $key );
 			}
 			else
 			{
	 			$this->error_fields = array ( $key, $key2 );
 			}
 			
			$this->replyError( $this->getResultDescription( 2 ) );
 		}
 	}

 	function checkUniqueExcept( $object_it, $key )
 	{
 		global $_REQUEST;

 		$it = $object_it->object->getByRefArray( 
 			array( $key => $_REQUEST[$key] )
 			);
 		
 		while ( !$it->end() )
 		{
 			if ( $it->getId() != $object_it->getId() )
 			{
				$this->replyError( $this->getResultDescription( 2 ) );
				break;
 			}
 			$it->moveNext();
 		}
 	}

 	function checkWordsCount( $field, $words )
 	{
 		global $_REQUEST;
 		
 		$result = array();
 		
 		if ( preg_match('/^([^\s\,\!\:\;\+\-]+[\s\r\,\!\:\;\+\-]*){1,'.$words.'}/i', $_REQUEST[$field], $result) === false )
 		{
 			$result[0] = $_REQUEST[$field];
 		}
 		else
 		{
 			if ( strlen($result[0]) < strlen($_REQUEST[$field]) )
 			{
 				$result[0] = trim($result[0]).'...';
 			}
 		}

		if ( $result[0] == $_REQUEST[$field] )
		{
			$this->replyError( $this->getResultDescription( 3 ) );
		}
 	}

	function getAction()
	{
 		global $_REQUEST;
 		
 		return $_REQUEST['action'];
	}
	
	function getResultDescription( $result )
	{
		switch($result)
		{
			case 1:
				return text(2);

			case 2:
				if ( is_object($this->error_object) )
				{
					if ( count($this->error_fields) > 1 )
					{
						$fields = array();
						
						foreach ( $this->error_fields as $field )
						{
							array_push( $fields,
								$this->error_object->getAttributeUserName($field) );
						}
						
						return str_replace('%2', $this->error_object->getDisplayName(),
							str_replace( '%1', '"'.join('", "', $fields).'"', text(460) ) );					
					}
					else
					{
						return str_replace('%2', $this->error_object->getDisplayName(),
							str_replace( '%1', $this->error_object->getAttributeUserName($this->error_fields[0]), text(459) ) );					
					}
				}
				else
				{
					return translate('Запись не уникальна');
				}

			default:
				return translate('Неизвестный результат');
		}
	}
	
	function validate()
	{
	}
	
	function modify( $object_id )
	{
	}
	
	function create()
	{
	}
	
	function delete( $object_id )
	{
	}
	
	function preview()
	{
	}

	function execute()
	{
		global $_REQUEST;
	 
		set_error_handler("CommandFormErrorHandler");
		$this->logStart();

        try {
            switch( $this->getAction() )
            {
                case CO_ACTION_CREATE:
                    if ( $this->validate() )
                    {
                        $this->create();
                    }
                    break;

                case CO_ACTION_PREVIEW:
                    if ( $this->validate() )
                    {
                        $this->preview();
                    }
                    break;

                case CO_ACTION_MODIFY:
                    if ( $this->validate() )
                    {
                        $this->modify( $_REQUEST['object_id'] );
                    }
                    break;

                case CO_ACTION_DELETE:
                    $this->delete( $_REQUEST['object_id'] );
                    break;
            }
        }
        catch( \Exception $e ) {
            $log = $this->getLogger();
            if ( is_object($log) ) $log->error( $e->getTraceAsString() );
            $this->replyError($e->getMessage());
        }

		$this->logFinish();
	}

	function replySuccess( $message = '', $object_id = '' )
	{
		$log = $this->getLogger();
		if ( is_object($log) && $message != '' ) $log->info( $message );
		
		$this->replyResult( false, $message, $object_id );
	}

	function replyRedirect( $url, $text = '' )
	{
		$this->_reply( 'redirect', $text, $url );
	}

	function replyResult( $is_error, $message, $object_id = '' )
	{
		$this->_reply( $is_error ? 'error' : 'success', $message, $object_id );
	}
	
	function replyDenied()
	{
		$this->replyError( text(983) );
	}
	
	function _reply( $state, $text, $object )
	{
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/html; charset=utf-8');

		$log = $this->getLogger();

		$result = array (
		    'state' => $state,
		    'message' => $text,
		    'object' => $object
		);
		
		if ( is_object($log) ) $log->info( $result );
		
		echo JsonWrapper::encode($result);

		if ( is_object($log) ) $log->info( str_replace('%1', get_class($this), text(1208)) );
		
		exit();		
	}
	
	function replyResultBinary( $is_error, $message, $object_id = '' )
	{
		$this->_reply( $is_error ? 'error' : 'success', $message, $object_id);  
	}

	function IsAuthenticationRequired() {
 	    return true;
    }
 }
 
 function CommandFormErrorHandler ($errno, $errmsg, $filename, $linenum, $vars) 
 {
 	global $PHP_SELF;
 	
    // set of errors for which a var trace will be saved
	if ( $errno == E_ERROR )	
    {
	    $command = new CommandForm;
	    $command->replyError( $errmsg );

        die();
    }
 } 
